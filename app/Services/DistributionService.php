<?php

namespace App\Services;

use App\Models\DistributionItem;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\EligibilityRecord;
use App\Models\Entitlement;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemPrice;
use App\Models\ItemVariant;
use App\Models\StockBalance;
use App\Models\Student;
use App\Models\StudentSizeHistory;
use App\Models\StudentSizeItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DistributionService
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly StudentSizeService $studentSizeService,
        private readonly EntitlementService $entitlementService,
    ) {}

    public function findStudent(string $query): ?Student
    {
        return Student::with(['studyProgram', 'generation', 'studentLevel', 'activeSizeProfile'])
            ->where('nim', $query)
            ->first();
    }

    public function getStudentEligibility(Student $student): ?EligibilityRecord
    {
        return $student->eligibilityRecords()->latest()->first();
    }

    public function isStudentEligible(Student $student): bool
    {
        $eligibility = $this->getStudentEligibility($student);

        return $eligibility && $eligibility->is_eligible;
    }

    public function getEntitlementForStudent(Student $student, ?DistributionSchedule $schedule = null): ?Entitlement
    {
        return $this->entitlementService->getEntitlement($student, $schedule);
    }

    /**
     * Find the specific item (by base_code + size) for distribution.
     * Returns the item with matching base_code and variant size.
     *
     * @param  array<int, int>|null  $allowedItemIds  Restrict candidates to items present in the schedule.
     */
    public function findItemByBaseCodeAndSize(string $baseCode, string $size, ?array $allowedItemIds = null): ?Item
    {
        $candidates = Item::where('base_code', $baseCode)
            ->when($allowedItemIds !== null, fn ($q) => $q->whereIn('id', $allowedItemIds))
            ->whereHas('variants', fn ($q) => $q->where('size', $size))
            ->with(['variants' => fn ($q) => $q->where('size', $size)])
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $balances = StockBalance::whereIn('item_id', $candidates->pluck('id'))
            ->whereIn('variant_id', $candidates->flatMap->variants->pluck('id'))
            ->get()
            ->keyBy(fn ($b) => $b->item_id.'-'.$b->variant_id);

        return $candidates->sortByDesc(function (Item $item) use ($balances) {
            $variant = $item->variants->first();
            if (! $variant) {
                return 0;
            }

            return $balances[$item->id.'-'.$variant->id]->quantity ?? 0;
        })->first();
    }

    public function processDistribution(
        Student $student,
        DistributionSchedule $schedule,
        User $staff,
        array $items,
        ?string $manualNote = null
    ): DistributionTransaction {
        return DB::transaction(function () use ($student, $schedule, $staff, $items, $manualNote) {
            Student::whereKey($student->id)->lockForUpdate()->first();

            $eligibility = EligibilityRecord::where('student_id', $student->id)
                ->lockForUpdate()
                ->first();
            if (! $eligibility || ! $eligibility->is_eligible) {
                throw new \Exception('Mahasiswa ini belum memenuhi syarat distribusi. Status pembayaran belum lunas.');
            }

            if (! $schedule->is_active) {
                throw new \Exception('Jadwal distribusi sudah tidak aktif. Silakan hubungi admin.');
            }

            if ($schedule->date && Carbon::parse($schedule->date)->isFuture()) {
                throw new \Exception('Jadwal distribusi "'.$schedule->name.'" belum dimulai.');
            }

            $isApplicable = DistributionSchedule::whereKey($schedule->id)
                ->forStudent($student)
                ->exists();
            if (! $isApplicable) {
                throw new \Exception(
                    'Jadwal distribusi "'.$schedule->name.'" tidak sesuai dengan mahasiswa ini. '.
                    'Pastikan fakultas/prodi/angkatan sesuai.'
                );
            }

            $scheduleItemIds = $schedule->items()->pluck('item_id')->all();
            $scheduleItemsById = Item::with(['variants', 'category'])
                ->whereIn('id', $scheduleItemIds)
                ->get()
                ->keyBy('id');
            $scheduleBaseCodes = $scheduleItemsById->pluck('base_code')->filter()->unique()->values()->all();
            $scheduleAllowedItems = empty($scheduleBaseCodes)
                ? $scheduleItemsById
                : Item::with(['variants', 'category'])
                    ->whereIn('base_code', $scheduleBaseCodes)
                    ->get()
                    ->keyBy('id');
            $submittedItemIds = array_values(array_unique(array_filter(array_map(
                fn ($row) => (int) ($row['item_id'] ?? 0),
                $items
            ))));
            $invalidItemIds = array_diff($submittedItemIds, $scheduleItemIds);
            if (! empty($invalidItemIds)) {
                throw new \Exception('Terdapat barang yang tidak termasuk dalam jadwal distribusi "'.$schedule->name.'".');
            }

            $entitlement = $this->getEntitlementForStudent($student, $schedule);
            $entitlementMap = $entitlement ? $entitlement->items->keyBy('item_id') : collect();
            $entitlementByBaseCode = $entitlement
                ? $entitlement->items->mapWithKeys(fn ($ei) => $ei->item?->base_code ? [$ei->item->base_code => $ei] : [])->all()
                : [];

            $context = $this->preloadDistributionContext(
                $student,
                $schedule,
                $items,
                $scheduleAllowedItems,
                $entitlement
            );

            $distributedByItem = DistributionItem::query()
                ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
                ->where('distribution_transactions.student_id', $student->id)
                ->where('distribution_transactions.status', '!=', 'cancelled')
                ->groupBy('distribution_items.item_id')
                ->selectRaw('distribution_items.item_id, SUM(distribution_items.quantity) as total')
                ->pluck('total', 'item_id')
                ->map(fn ($v) => (int) $v);

            $distributedByBaseCode = DistributionItem::query()
                ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
                ->join('items', 'distribution_items.item_id', '=', 'items.id')
                ->where('distribution_transactions.student_id', $student->id)
                ->where('distribution_transactions.status', '!=', 'cancelled')
                ->whereNotNull('items.base_code')
                ->groupBy('items.base_code')
                ->selectRaw('items.base_code, SUM(distribution_items.quantity) as total')
                ->pluck('total', 'base_code')
                ->map(fn ($v) => (int) $v);

            $hasPendingEntitlement = false;
            if ($entitlement && $entitlement->items->isNotEmpty()) {
                foreach ($entitlement->items as $entItem) {
                    $itemCode = $entItem->item?->base_code;
                    $taken = $itemCode
                        ? ($distributedByBaseCode[$itemCode] ?? 0)
                        : ($distributedByItem[$entItem->item_id] ?? 0);
                    if ($taken < (int) $entItem->quantity) {
                        $hasPendingEntitlement = true;
                        break;
                    }
                }
            }

            $existingTransactions = DistributionTransaction::where('student_id', $student->id)
                ->where('schedule_id', $schedule->id)
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->exists();

            if ($existingTransactions && ! $hasPendingEntitlement) {
                throw new \Exception('Mahasiswa ini sudah melakukan pengambilan pada jadwal "'.$schedule->name.'". Tidak boleh mengambil ulang.');
            }

            $transaction = DistributionTransaction::create([
                'student_id' => $student->id,
                'schedule_id' => $schedule->id,
                'staff_id' => $staff->id,
                'status' => 'completed',
                'pickup_time' => now(),
                'notes' => $manualNote,
            ]);

            $allFullyStocked = true;
            $autoNotes = [];
            $distributedInRequest = [];
            $distributionRows = [];
            $fifoRequests = [];

            foreach ($items as $index => $itemData) {
                $submittedItem = $context['submitted']->get((int) ($itemData['item_id'] ?? 0));
                $submittedBaseCode = $itemData['base_code'] ?? $submittedItem?->base_code;

                if ($submittedItem && $submittedBaseCode && $submittedItem->base_code !== $submittedBaseCode) {
                    throw new \Exception(
                        "Barang \"{$submittedItem->name}\" tidak sesuai dengan kode barang yang dikirim ({$submittedBaseCode})."
                    );
                }

                $item = $submittedBaseCode
                    ? $this->resolveItemFromContext($context, $submittedBaseCode, $itemData['actual_size'])
                    : null;
                $item ??= $submittedItem;

                if (! $item) {
                    continue;
                }

                if ($student->gender && $item->gender && $item->gender !== 'U' && $student->gender !== $item->gender) {
                    throw new \Exception(
                        "Item \"{$item->name}\" ({$item->gender}) tidak sesuai dengan gender mahasiswa ({$student->gender})."
                    );
                }

                $entitlementItem = $entitlementMap->get($item->id)
                    ?? ($entitlementByBaseCode[$item->base_code ?? ''] ?? null);
                $productKey = $item->base_code ?? (string) $item->id;
                $requestedQty = (int) ($itemData['quantity'] ?? 1);

                if ($entitlementItem) {
                    $alreadyDistributed = (int) ($item->base_code
                            ? ($distributedByBaseCode[$item->base_code] ?? 0)
                            : ($distributedByItem[$item->id] ?? 0))
                        + (int) ($distributedInRequest[$productKey] ?? 0);
                    $remaining = (int) $entitlementItem->quantity - $alreadyDistributed;

                    if ($remaining <= 0) {
                        throw new \Exception(
                            "Hak barang {$item->name} untuk mahasiswa ini sudah terpenuhi."
                        );
                    }

                    if ($requestedQty > $remaining) {
                        throw new \Exception(
                            "Jumlah {$item->name} melebihi hak distribusi ({$requestedQty} diminta, sisa hak {$remaining})."
                        );
                    }
                }

                $variant = $item->variants->firstWhere('size', $itemData['actual_size']);

                $quantity = (int) ($itemData['quantity'] ?? 1);
                $deductedQty = 0;

                if (! $variant) {
                    $allFullyStocked = false;
                    $autoNotes[] = "Stok {$item->name} (Ukuran {$itemData['actual_size']}) tidak tersedia";
                } else {
                    $availableStock = (int) ($context['balances']->get($item->id.'-'.$variant->id)?->quantity ?? 0);
                    $deductedQty = min($quantity, $availableStock);

                    if ($availableStock < $quantity) {
                        $allFullyStocked = false;
                        $shortage = $quantity - $availableStock;
                        $autoNotes[] = "Stok {$item->name} (Ukuran {$variant->size}) habis/kurang (kurang {$shortage} pcs)";
                    }

                    if ($deductedQty > 0) {
                        $fifoRequests[$index] = [
                            'item_id' => $item->id,
                            'variant_id' => $variant->id,
                            'quantity' => $deductedQty,
                        ];
                    }
                }

                $effectiveQty = $variant ? $deductedQty : 0;

                if ($entitlementItem) {
                    $distributedInRequest[$productKey] = ($distributedInRequest[$productKey] ?? 0) + $effectiveQty;
                }

                $sellingPrice = $context['deferredPrices']->get($item->id)
                    ?? $context['prices']->get($item->id)
                    ?? (float) $item->selling_price;

                $distributionRows[$index] = [
                    'transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'variant_id' => $variant?->id,
                    'expected_size' => $itemData['expected_size'] ?? $itemData['actual_size'],
                    'actual_size' => $itemData['actual_size'],
                    'quantity' => $effectiveQty,
                    'hpp' => 0,
                    'unit_price' => $sellingPrice,
                    'selling_price_at_distribution' => $sellingPrice,
                ];

                $oldSize = $itemData['old_size'] ?? $itemData['expected_size'] ?? null;
                if ($oldSize && $oldSize !== $itemData['actual_size'] && $oldSize !== '-') {
                    $this->logSizeChange($student, $item, $oldSize, $itemData['actual_size'], $staff);
                }
            }

            if ($fifoRequests !== []) {
                $fifoResults = $this->stockService->deductStockFifos(
                    array_values($fifoRequests),
                    DistributionTransaction::class,
                    $transaction->id,
                    "Distribusi ke {$student->nim}"
                );

                foreach (array_keys($fifoRequests) as $position => $index) {
                    $distributionRows[$index]['hpp'] = $fifoResults[$position]['blended_hpp'];
                }
            }

            $this->insertDistributionItems($distributionRows);

            // Check if there are items in entitlement that were not checked (deferred)
            $checkedBaseCodes = [];
            foreach ($items as $itemData) {
                if (! empty($itemData['base_code'])) {
                    $checkedBaseCodes[] = $itemData['base_code'];
                } elseif (! empty($itemData['item_id'])) {
                    $baseCode = $context['submitted']->get((int) $itemData['item_id'])?->base_code;
                    if ($baseCode) {
                        $checkedBaseCodes[] = $baseCode;
                    }
                }
            }

            $studentSizesByBaseCode = [];
            $sizeProfile = $student->activeSizeProfile;
            if ($sizeProfile && $entitlement) {
                $categories = [];

                foreach ($context['submitted'] as $contextItem) {
                    if ($contextItem->category) {
                        $categories[$contextItem->category_id] = $contextItem->category->code;
                    }
                }

                foreach ($context['byBaseCode'] as $contextGroup) {
                    foreach ($contextGroup as $contextItem) {
                        if ($contextItem->category) {
                            $categories[$contextItem->category_id] = $contextItem->category->code;
                        }
                    }
                }

                $missingCategoryIds = $entitlement->items
                    ->pluck('item.category_id')
                    ->filter()
                    ->unique()
                    ->reject(fn ($id) => array_key_exists($id, $categories))
                    ->values();

                if ($missingCategoryIds->isNotEmpty()) {
                    $categories += ItemCategory::whereIn('id', $missingCategoryIds)->pluck('code', 'id')->all();
                }

                foreach ($entitlement->items as $entitlementItem) {
                    $item = $entitlementItem->item;
                    if (! $item) {
                        continue;
                    }

                    $catCode = $categories[$item->category_id] ?? null;
                    $stored = $catCode === 'UNF' ? $sizeProfile->baju_size
                        : ($catCode === 'SHO' ? $sizeProfile->sepatu_size : null);

                    if (empty($stored)) {
                        continue;
                    }

                    $resolved = $this->studentSizeService->resolveSizeValue($item, $stored);
                    $label = $resolved['label'] ?? $stored;

                    $studentSizesByBaseCode[$entitlementItem->item_id] = $label;
                    if ($item->base_code) {
                        $studentSizesByBaseCode[$item->base_code] = $label;
                    }
                }
            }
            if ($entitlement) {
                foreach ($entitlement->items as $entitlementItem) {
                    $entBaseCode = $entitlementItem->item?->base_code;
                    if ($entBaseCode && in_array($entBaseCode, $checkedBaseCodes)) {
                        continue;
                    }
                    if (in_array($entitlementItem->item_id, array_column($items, 'item_id'))) {
                        continue;
                    }

                    $productKey = $entBaseCode ?? (string) $entitlementItem->item_id;
                    $alreadyDistributedTotal = (int) ($entBaseCode
                            ? ($distributedByBaseCode[$entBaseCode] ?? 0)
                            : ($distributedByItem[$entitlementItem->item_id] ?? 0))
                        + (int) ($distributedInRequest[$productKey] ?? 0);

                    if ($alreadyDistributedTotal >= (int) $entitlementItem->quantity) {
                        continue;
                    }

                    $allFullyStocked = false;
                    $expectedSize = $studentSizesByBaseCode[$entitlementItem->item_id]
                        ?? $studentSizesByBaseCode[$entBaseCode ?? ''] ?? '-';
                    $autoNotes[] = ($entitlementItem->item?->name ?? 'Barang')." (Ukuran {$expectedSize}) ditunda/belum diambil";
                }
            }

            if (! $allFullyStocked) {
                $finalNotes = $manualNote;
                if (! empty($autoNotes)) {
                    $autoNotesStr = 'Sistem: '.implode(' | ', $autoNotes);
                    $finalNotes = $finalNotes ? $finalNotes.' | '.$autoNotesStr : $autoNotesStr;
                }
                $transaction->update([
                    'status' => 'partial',
                    'notes' => $finalNotes,
                ]);
            }

            $loadedTransaction = null;

            DB::afterCommit(function () use (&$loadedTransaction) {
                $transaction = $loadedTransaction;

                if (! $transaction instanceof DistributionTransaction) {
                    return;
                }

                app(NotificationService::class)->sendDistributionConfirmation($transaction);

                if ($transaction->status === 'partial') {
                    app(NotificationService::class)->sendShortageAlert($transaction);
                }
            });

            $distributionItems = $transaction->items()->get();
            $distributionItems->each(function (DistributionItem $distributionItem) use ($scheduleAllowedItems, $context) {
                $item = $scheduleAllowedItems->get($distributionItem->item_id)
                    ?? $context['submitted']->get($distributionItem->item_id);

                if ($item) {
                    $distributionItem->setRelation('item', $item);
                }
            });

            $transaction->setRelation('items', $distributionItems);
            $transaction->setRelation('student', $student);
            $transaction->setRelation('schedule', $schedule);

            $loadedTransaction = $transaction;

            return $loadedTransaction;
        }, attempts: 5);
    }

    /**
     * Preload every item, variant, balance, price and deferred price the distribution
     * loop needs, so the loop itself never issues a query per row.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  Collection<int, Item>  $allowedItems
     * @return array{
     *     submitted: Collection<int, Item>,
     *     byBaseCode: Collection<string, Collection<int, Item>>,
     *     balances: Collection<string, StockBalance>,
     *     prices: Collection<int, float>,
     *     deferredPrices: Collection<int, float>
     * }
     */
    private function preloadDistributionContext(
        Student $student,
        DistributionSchedule $schedule,
        array $items,
        Collection $allowedItems,
        ?Entitlement $entitlement
    ): array {
        $submittedIds = collect($items)
            ->pluck('item_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $submitted = $submittedIds === []
            ? collect()
            : $allowedItems->whereIn('id', $submittedIds)->keyBy('id');

        $baseCodes = collect($items)
            ->map(fn (array $row) => $row['base_code'] ?? null)
            ->filter()
            ->merge($submitted->pluck('base_code')->filter())
            ->unique()
            ->values()
            ->all();

        $byBaseCode = $baseCodes === []
            ? collect()
            : $allowedItems
                ->filter(fn (Item $item) => in_array($item->base_code, $baseCodes, true))
                ->groupBy('base_code');

        $this->hydrateEntitlementItems($entitlement, $submitted, $byBaseCode);

        $relevantIds = collect($submitted->keys())
            ->merge($byBaseCode->flatten()->pluck('id'))
            ->merge($entitlement ? $entitlement->items->pluck('item_id') : [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($relevantIds === []) {
            return [
                'submitted' => $submitted,
                'byBaseCode' => $byBaseCode,
                'balances' => collect(),
                'prices' => collect(),
                'deferredPrices' => collect(),
            ];
        }

        $balances = StockBalance::whereIn('item_id', $relevantIds)
            ->get()
            ->keyBy(fn (StockBalance $balance) => $balance->item_id.'-'.$balance->variant_id);

        $prices = ItemPrice::whereIn('item_id', $relevantIds)
            ->where('effective_date', '<=', $schedule->date)
            ->orderByDesc('effective_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('item_id')
            ->map(fn ($group) => (float) $group->first()->selling_price);

        $deferredPrices = DistributionItem::query()
            ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
            ->whereIn('distribution_items.item_id', $relevantIds)
            ->where('distribution_transactions.student_id', $student->id)
            ->where('distribution_transactions.status', 'partial')
            ->orderByDesc('distribution_items.id')
            ->get(['distribution_items.item_id', 'distribution_items.selling_price_at_distribution'])
            ->groupBy('item_id')
            ->map(fn ($group) => (float) $group->first()->selling_price_at_distribution)
            ->filter(fn (float $price) => $price > 0);

        return [
            'submitted' => $submitted,
            'byBaseCode' => $byBaseCode,
            'balances' => $balances,
            'prices' => $prices,
            'deferredPrices' => $deferredPrices,
        ];
    }

    /**
     * Attach variants/category to the entitlement item models using the already
     * loaded collections, so size resolution does not query per entitlement row.
     *
     * @param  Collection<int, Item>  $submitted
     * @param  Collection<string, Collection<int, Item>>  $byBaseCode
     */
    private function hydrateEntitlementItems(?Entitlement $entitlement, $submitted, $byBaseCode): void
    {
        if (! $entitlement || $entitlement->items->isEmpty()) {
            return;
        }

        $known = $submitted->all();
        foreach ($byBaseCode as $group) {
            foreach ($group as $item) {
                $known[$item->id] = $item;
            }
        }

        $missingIds = $entitlement->items
            ->pluck('item_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->reject(fn (int $id) => isset($known[$id]))
            ->unique()
            ->values()
            ->all();

        if ($missingIds !== []) {
            foreach (Item::with(['variants', 'category'])->whereIn('id', $missingIds)->get() as $item) {
                $known[$item->id] = $item;
            }
        }

        foreach ($entitlement->items as $entitlementItem) {
            $source = $known[(int) $entitlementItem->item_id] ?? null;

            if (! $source || ! $entitlementItem->item) {
                continue;
            }

            $entitlementItem->item->setRelation('variants', $source->variants);
            $entitlementItem->item->setRelation('category', $source->category);
        }
    }

    /**
     * Resolve the best item row for a base code + size from the preloaded context,
     * preferring the row that still has stock.
     *
     * @param  array{byBaseCode: Collection<string, Collection<int, Item>>, balances: Collection<string, StockBalance>}  $context
     */
    private function resolveItemFromContext(array $context, string $baseCode, ?string $size): ?Item
    {
        $candidates = $context['byBaseCode']->get($baseCode);

        if (! $candidates) {
            return null;
        }

        $matching = $candidates->filter(
            fn (Item $item) => $item->variants->contains(fn (ItemVariant $variant) => $variant->size === $size)
        );

        if ($matching->isEmpty()) {
            return null;
        }

        return $matching->sortByDesc(function (Item $item) use ($context, $size) {
            $variant = $item->variants->first(fn (ItemVariant $candidate) => $candidate->size === $size);

            if (! $variant) {
                return 0;
            }

            return $context['balances']->get($item->id.'-'.$variant->id)?->quantity ?? 0;
        })->first();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function insertDistributionItems(array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $now = now();
        foreach ($rows as $index => $row) {
            $rows[$index]['created_at'] = $now;
            $rows[$index]['updated_at'] = $now;
        }

        DistributionItem::insert(array_values($rows));
    }

    private function logSizeChange(Student $student, Item $item, string $oldSize, string $newSize, User $staff): void
    {
        $student->loadMissing('activeSizeProfile.sizeItems');
        $sizeProfile = $student->activeSizeProfile;
        if (! $sizeProfile) {
            return;
        }

        $item->loadMissing('category');

        $resolved = $this->studentSizeService->resolveSizeValue($item, $newSize);
        $newCode = $resolved['code'] ?? $newSize;
        $newLabel = $resolved['label'] ?? $newSize;

        $sizeItem = StudentSizeItem::where('size_profile_id', $sizeProfile->id)
            ->where('item_id', $item->id)
            ->first();

        if (! $sizeItem) {
            $sizeItem = StudentSizeItem::create([
                'size_profile_id' => $sizeProfile->id,
                'item_id' => $item->id,
                'size' => $newCode,
                'change_count' => 1,
            ]);

            StudentSizeHistory::create([
                'size_item_id' => $sizeItem->id,
                'old_size' => $oldSize,
                'new_size' => $newCode,
                'changed_by' => $staff->id,
                'changed_at' => now(),
            ]);
        } else {
            StudentSizeHistory::create([
                'size_item_id' => $sizeItem->id,
                'old_size' => $oldSize,
                'new_size' => $newCode,
                'changed_by' => $staff->id,
                'changed_at' => now(),
            ]);

            $sizeItem->update([
                'size' => $newCode,
                'change_count' => $sizeItem->change_count + 1,
            ]);
        }

        $catCode = $item->category?->code;
        if ($catCode === 'UNF') {
            $sizeProfile->update(['baju_size' => $newLabel]);
        } elseif ($catCode === 'SHO') {
            $sizeProfile->update(['sepatu_size' => $newLabel]);
        }
    }
}
