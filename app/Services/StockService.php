<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\StockBalance;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\StockReceive;
use App\Models\StockReceiveItem;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function receiveStock(array $data): StockReceive
    {
        return DB::transaction(function () use ($data) {
            $receive = StockReceive::create([
                'reference_number' => $data['reference_number'] ?? $this->generateReferenceNumber(),
                'vendor_id' => $data['vendor_id'],
                'receive_date' => $data['receive_date'],
                'status' => 'received',
                'notes' => $data['notes'] ?? null,
            ]);

            $itemIds = array_column($data['items'], 'item_id');
            $variantIds = array_column($data['items'], 'variant_id');
            $preloadedItems = Item::whereIn('id', $itemIds)->get()->keyBy('id');
            $preloadedVariants = ItemVariant::whereIn('id', $variantIds)->get()->keyBy('id');

            foreach ($data['items'] as $itemData) {
                $item = $preloadedItems->get($itemData['item_id']);
                $variant = $preloadedVariants->get($itemData['variant_id']);
                if (! $item || ! $variant) {
                    continue;
                }

                $receiveItem = StockReceiveItem::create([
                    'stock_receive_id' => $receive->id,
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'hpp' => $itemData['hpp'] ?? $itemData['unit_price'] ?? 0,
                ]);

                StockMovement::create([
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                    'type' => 'IN',
                    'quantity' => $itemData['quantity'],
                    'hpp' => $receiveItem->hpp,
                    'reference_type' => StockReceive::class,
                    'reference_id' => $receive->id,
                    'notes' => "Penerimaan dari vendor: {$receive->reference_number}",
                ]);

                StockBatch::create([
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                    'quantity_remaining' => $itemData['quantity'],
                    'unit_hpp' => $receiveItem->hpp,
                    'received_date' => $data['receive_date'],
                    'stock_receive_item_id' => $receiveItem->id,
                ]);

                $this->upsertBalance(
                    $item->id,
                    $variant->id,
                    $itemData['quantity'],
                    $receiveItem->hpp
                );
            }

            $this->forgetDashboardStockCache();

            return $receive->fresh(['items.item', 'items.variant', 'vendor']);
        }, attempts: 5);
    }

    private function upsertBalance(int $itemId, ?int $variantId, int $addedQty, float $newHpp): void
    {
        $balance = StockBalance::where('item_id', $itemId)
            ->where('variant_id', $variantId)
            ->lockForUpdate()
            ->first();

        if ($balance) {
            $oldQty = $balance->quantity;
            $oldHpp = $balance->last_hpp;
            $totalQty = $oldQty + $addedQty;
            $avgHpp = $totalQty > 0
                ? (($oldQty * $oldHpp) + ($addedQty * $newHpp)) / $totalQty
                : $newHpp;

            $balance->update([
                'quantity' => $totalQty,
                'last_hpp' => round($avgHpp, 2),
            ]);
        } else {
            retry(5, function () use ($itemId, $variantId, $addedQty, $newHpp) {
                try {
                    StockBalance::create([
                        'item_id' => $itemId,
                        'variant_id' => $variantId,
                        'quantity' => $addedQty,
                        'last_hpp' => round($newHpp, 2),
                    ]);
                } catch (QueryException $e) {
                    if ((int) $e->getCode() !== 23000) {
                        throw $e;
                    }

                    $existing = StockBalance::where('item_id', $itemId)
                        ->where('variant_id', $variantId)
                        ->lockForUpdate()
                        ->first();

                    if ($existing) {
                        $oldQty = $existing->quantity;
                        $oldHpp = $existing->last_hpp;
                        $totalQty = $oldQty + $addedQty;
                        $avgHpp = $totalQty > 0
                            ? (($oldQty * $oldHpp) + ($addedQty * $newHpp)) / $totalQty
                            : $newHpp;

                        $existing->update([
                            'quantity' => $totalQty,
                            'last_hpp' => round($avgHpp, 2),
                        ]);

                        return;
                    }

                    throw $e;
                }
            }, 100);
        }
    }

    public function increaseBalance(int $itemId, ?int $variantId, int $quantity, float $unitHpp = 0): void
    {
        DB::transaction(function () use ($itemId, $variantId, $quantity, $unitHpp) {
            $this->increaseBalanceLocked($itemId, $variantId, $quantity, $unitHpp);
        });

        $this->forgetDashboardStockCache();
    }

    private function increaseBalanceLocked(int $itemId, ?int $variantId, int $quantity, float $unitHpp = 0): void
    {
        $balance = StockBalance::where('item_id', $itemId)
            ->where('variant_id', $variantId)
            ->lockForUpdate()
            ->first();

        if ($balance) {
            $oldQty = $balance->quantity;
            $oldHpp = (float) $balance->last_hpp;
            $totalQty = $oldQty + $quantity;
            $avgHpp = $totalQty > 0
                ? (($oldQty * $oldHpp) + ($quantity * $unitHpp)) / $totalQty
                : $unitHpp;

            $balance->update([
                'quantity' => $totalQty,
                'last_hpp' => round($avgHpp, 2),
            ]);
        } else {
            StockBalance::create([
                'item_id' => $itemId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'last_hpp' => round($unitHpp, 2),
            ]);
        }
    }

    /**
     * Build a single-statement `CASE` update so a batch of rows can be written in one query.
     *
     * @param  array<int, int>  $values  keyed by the row identifier
     */
    private function caseWhenSql(string $column, array $values): string
    {
        $cases = '';
        foreach ($values as $key => $value) {
            $cases .= 'WHEN '.(int) $key.' THEN '.(int) $value.' ';
        }

        return 'CASE '.$column.' '.$cases.'END';
    }

    private function forgetDashboardStockCache(): void
    {
        cache()->forget('dashboard-shortage-items');
        cache()->forget('dashboard-out-of-stock-items');
    }

    public function getBalance(int $itemId, ?int $variantId = null): ?StockBalance
    {
        return StockBalance::where('item_id', $itemId)
            ->where('variant_id', $variantId)
            ->first();
    }

    public function getBalanceByItem(Item $item): Collection
    {
        return StockBalance::with('variant')
            ->where('item_id', $item->id)
            ->get();
    }

    public function getAllBalances(?string $category = null, ?string $gender = null): Collection
    {
        $query = StockBalance::with(['item.category', 'variant'])
            ->join('items', 'stock_balances.item_id', '=', 'items.id')
            ->leftJoin('item_variants', 'stock_balances.variant_id', '=', 'item_variants.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->select(
                'stock_balances.*',
                'items.name as item_name',
                'items.code as item_code',
                'items.selling_price',
                'items.unit',
                'item_categories.label as category_name',
                'item_categories.code as category_code',
                'item_variants.size as variant_size'
            )
            ->orderBy('item_categories.code')
            ->orderBy('items.name');

        if ($category) {
            $query->where('item_categories.code', $category);
        }

        if ($gender) {
            $query->where('items.gender', $gender);
        }

        return $query->get();
    }

    public function getMovements(int $itemId, ?int $variantId = null, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = StockMovement::with(['item', 'variant'])
            ->where('item_id', $itemId)
            ->orderBy('created_at')
            ->orderBy('id');

        if ($variantId) {
            $query->where('variant_id', $variantId);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->get();
    }

    public function deductStockFifo(int $itemId, int $variantId, int $quantity, string $referenceType, int $referenceId, ?string $notes = null): array
    {
        $results = $this->deductStockFifos(
            [['item_id' => $itemId, 'variant_id' => $variantId, 'quantity' => $quantity]],
            $referenceType,
            $referenceId,
            $notes
        );

        return $results[0] ?? [
            'blended_hpp' => 0.0,
            'total_cost' => 0.0,
            'consumed_batches' => [],
            'movement_ids' => [],
        ];
    }

    /**
     * Deduct stock for several item/variant pairs inside ONE transaction.
     *
     * Balances, batches and movements are read/written in bulk so the query count
     * stays constant regardless of how many pairs are deducted.
     *
     * @param  array<int, array{item_id: int, variant_id: int, quantity: int}>  $requests
     * @return array<int, array{blended_hpp: float, total_cost: float, consumed_batches: array<int, array{batch_id: int, quantity: int, unit_hpp: mixed}>, movement_ids: array<int, int>}>
     */
    public function deductStockFifos(array $requests, string $referenceType, int $referenceId, ?string $notes = null): array
    {
        $requests = array_values(array_filter(
            $requests,
            fn (array $request) => (int) $request['quantity'] > 0
        ));

        if ($requests === []) {
            return [];
        }

        return DB::transaction(function () use ($requests, $referenceType, $referenceId, $notes) {
            $itemIds = array_values(array_unique(array_map(fn (array $r) => (int) $r['item_id'], $requests)));
            $variantIds = array_values(array_unique(array_map(fn (array $r) => (int) $r['variant_id'], $requests)));

            $balances = StockBalance::whereIn('item_id', $itemIds)
                ->whereIn('variant_id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy(fn (StockBalance $balance) => $balance->item_id.'-'.$balance->variant_id);

            $batches = StockBatch::whereIn('item_id', $itemIds)
                ->whereIn('variant_id', $variantIds)
                ->where('quantity_remaining', '>', 0)
                ->orderBy('received_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->groupBy(fn (StockBatch $batch) => $batch->item_id.'-'.$batch->variant_id);

            $batchQuantities = [];
            $balanceQuantities = [];
            $movementRows = [];
            $results = [];

            foreach ($requests as $request) {
                $itemId = (int) $request['item_id'];
                $variantId = (int) $request['variant_id'];
                $quantity = (int) $request['quantity'];
                $key = $itemId.'-'.$variantId;

                $balance = $balances->get($key);
                $availableBalance = $balance
                    ? ($balanceQuantities[$balance->id] ?? (int) $balance->quantity)
                    : 0;

                if (! $balance || $availableBalance < $quantity) {
                    throw new \Exception("Stok tidak mencukupi untuk item #{$itemId} varian #{$variantId}.");
                }

                $pairBatches = $batches->get($key, collect());
                $availableInBatches = (int) $pairBatches->sum(
                    fn (StockBatch $batch) => $batchQuantities[$batch->id] ?? (int) $batch->quantity_remaining
                );

                if ($availableInBatches < $quantity) {
                    throw new \Exception(
                        "Saldo batch tidak mencukupi untuk item #{$itemId} varian #{$variantId} (batch: {$availableInBatches}, diminta: {$quantity}). Lakukan Stock Opname untuk menyelaraskan saldo."
                    );
                }

                $remaining = $quantity;
                $totalCost = 0.0;
                $consumedBatches = [];

                foreach ($pairBatches as $batch) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $batchAvailable = $batchQuantities[$batch->id] ?? (int) $batch->quantity_remaining;
                    $consume = min($remaining, $batchAvailable);

                    if ($consume <= 0) {
                        continue;
                    }

                    $batchQuantities[$batch->id] = $batchAvailable - $consume;

                    $movementRows[] = [
                        'item_id' => $itemId,
                        'variant_id' => $variantId,
                        'type' => 'OUT',
                        'quantity' => $consume,
                        'hpp' => $batch->unit_hpp,
                        'stock_batch_id' => $batch->id,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                        'notes' => $notes ?? 'Pengeluaran stok (FIFO)',
                    ];

                    $totalCost += $consume * (float) $batch->unit_hpp;
                    $consumedBatches[] = [
                        'batch_id' => $batch->id,
                        'quantity' => $consume,
                        'unit_hpp' => $batch->unit_hpp,
                    ];

                    $remaining -= $consume;
                }

                $balanceQuantities[$balance->id] = $availableBalance - $quantity;

                $results[] = [
                    'blended_hpp' => round($totalCost / $quantity, 2),
                    'total_cost' => $totalCost,
                    'consumed_batches' => $consumedBatches,
                    'movement_ids' => [],
                ];
            }

            $this->insertStockMovements($movementRows);
            $this->applyQuantityUpdates('stock_batches', 'quantity_remaining', $batchQuantities);
            $this->applyQuantityUpdates('stock_balances', 'quantity', $balanceQuantities);

            $movementIds = StockMovement::where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->orderBy('id')
                ->pluck('id')
                ->all();

            // The same reference may be reused across calls (e.g. one stock opname);
            // only the rows this call inserted belong to these results.
            $insertedCount = count($movementRows);
            if ($insertedCount > 0 && count($movementIds) > $insertedCount) {
                $movementIds = array_slice($movementIds, -$insertedCount);
            }

            $this->forgetDashboardStockCache();

            $offset = 0;
            foreach ($results as $position => $result) {
                $consumed = count($result['consumed_batches']);
                $results[$position]['movement_ids'] = array_slice($movementIds, $offset, $consumed);
                $offset += $consumed;
            }

            return $results;
        }, attempts: 5);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function insertStockMovements(array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $now = now();
        foreach ($rows as $index => $row) {
            $rows[$index]['created_at'] = $now;
            $rows[$index]['updated_at'] = $now;
        }

        StockMovement::insert($rows);
    }

    /**
     * Apply many absolute quantity updates with a single CASE statement.
     *
     * @param  array<int, int>  $quantities  keyed by row id
     */
    private function applyQuantityUpdates(string $table, string $column, array $quantities): void
    {
        if ($quantities === []) {
            return;
        }

        $cases = '';
        foreach ($quantities as $id => $quantity) {
            $cases .= ' WHEN '.(int) $id.' THEN '.(int) $quantity;
        }

        DB::table($table)
            ->whereIn('id', array_keys($quantities))
            ->update([$column => DB::raw('CASE id'.$cases.' END')]);
    }

    public function getPendingDemandCounts(): Collection
    {
        return DB::table('student_size_items')
            ->join('student_size_profiles', 'student_size_items.size_profile_id', '=', 'student_size_profiles.id')
            ->join('students', 'student_size_profiles.student_id', '=', 'students.id')
            ->join('items', 'student_size_items.item_id', '=', 'items.id')
            ->where('students.status', 'active')
            ->whereNull('students.deleted_at')
            ->where('student_size_profiles.is_filled', true)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('distribution_items as di')
                    ->join('distribution_transactions as dt', 'di.transaction_id', '=', 'dt.id')
                    ->whereColumn('dt.student_id', 'students.id')
                    ->where('dt.status', '!=', 'cancelled')
                    ->where('di.quantity', '>', 0)
                    ->where(function ($matchQuery) {
                        $matchQuery->whereColumn('di.item_id', 'student_size_items.item_id')
                            ->orWhere(function ($baseCodeQuery) {
                                $baseCodeQuery->whereNotNull('items.base_code')
                                    ->where('items.base_code', '!=', '')
                                    ->whereExists(function ($sub) {
                                        $sub->select(DB::raw(1))
                                            ->from('items as i2')
                                            ->whereColumn('i2.id', 'di.item_id')
                                            ->whereColumn('i2.base_code', 'items.base_code');
                                    });
                            });
                    });
            })
            ->select('student_size_items.item_id', 'student_size_items.size')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('student_size_items.item_id', 'student_size_items.size')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->item_id.'|'.$row->size => (int) $row->total]);
    }

    public function getDemandShortageItems(): Collection
    {
        return DB::table('student_size_items')
            ->join('student_size_profiles', 'student_size_items.size_profile_id', '=', 'student_size_profiles.id')
            ->join('students', 'student_size_profiles.student_id', '=', 'students.id')
            ->join('items', 'student_size_items.item_id', '=', 'items.id')
            ->leftJoin('item_variants', function ($join) {
                $join->on('student_size_items.item_id', '=', 'item_variants.item_id')
                    ->on('student_size_items.size', '=', 'item_variants.size');
            })
            ->leftJoin('stock_balances', function ($join) {
                $join->on('item_variants.id', '=', 'stock_balances.variant_id');
            })
            ->where('students.status', 'active')
            ->whereNull('students.deleted_at')
            ->where('student_size_profiles.is_filled', true)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('distribution_items as di')
                    ->join('distribution_transactions as dt', 'di.transaction_id', '=', 'dt.id')
                    ->whereColumn('dt.student_id', 'students.id')
                    ->where('dt.status', '!=', 'cancelled')
                    ->where('di.quantity', '>', 0)
                    ->where(function ($matchQuery) {
                        $matchQuery->whereColumn('di.item_id', 'student_size_items.item_id')
                            ->orWhere(function ($baseCodeQuery) {
                                $baseCodeQuery->whereNotNull('items.base_code')
                                    ->where('items.base_code', '!=', '')
                                    ->whereExists(function ($sub) {
                                        $sub->select(DB::raw(1))
                                            ->from('items as i2')
                                            ->whereColumn('i2.id', 'di.item_id')
                                            ->whereColumn('i2.base_code', 'items.base_code');
                                    });
                            });
                    });
            })
            ->select(
                'items.id as item_id',
                'items.name as item_name',
                'items.code as item_code',
                'items.unit',
                'student_size_items.size',
                DB::raw('COALESCE(item_variants.size_label, student_size_items.size) as size_label'),
                DB::raw('COALESCE(item_variants.sku, CONCAT(items.code, \'-\', student_size_items.size)) as sku'),
                DB::raw('COUNT(*) as demand'),
                DB::raw('COALESCE(MAX(stock_balances.quantity), 0) as stock')
            )
            ->groupBy(
                'items.id',
                'items.name',
                'items.code',
                'items.unit',
                'student_size_items.size',
                'item_variants.size_label',
                'item_variants.sku'
            )
            ->havingRaw('COUNT(*) > COALESCE(MAX(stock_balances.quantity), 0)')
            ->orderBy('items.name')
            ->orderBy('student_size_items.size')
            ->get()
            ->map(function ($row) {
                $row->shortage = $row->demand - $row->stock;
                $row->status = $row->stock <= 0 ? 'out_of_stock' : 'shortage';

                return $row;
            });
    }

    public function getDemandData(): Collection
    {
        return DB::table('student_size_items')
            ->join('student_size_profiles', 'student_size_items.size_profile_id', '=', 'student_size_profiles.id')
            ->join('students', 'student_size_profiles.student_id', '=', 'students.id')
            ->join('items', 'student_size_items.item_id', '=', 'items.id')
            ->leftJoin('item_variants', function ($join) {
                $join->on('student_size_items.item_id', '=', 'item_variants.item_id')
                    ->on('student_size_items.size', '=', 'item_variants.size');
            })
            ->leftJoin('stock_balances', function ($join) {
                $join->on('item_variants.id', '=', 'stock_balances.variant_id');
            })
            ->where('students.status', 'active')
            ->whereNull('students.deleted_at')
            ->where('student_size_profiles.is_filled', true)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('distribution_items as di')
                    ->join('distribution_transactions as dt', 'di.transaction_id', '=', 'dt.id')
                    ->whereColumn('dt.student_id', 'students.id')
                    ->where('dt.status', '!=', 'cancelled')
                    ->where('di.quantity', '>', 0)
                    ->where(function ($matchQuery) {
                        $matchQuery->whereColumn('di.item_id', 'student_size_items.item_id')
                            ->orWhere(function ($baseCodeQuery) {
                                $baseCodeQuery->whereNotNull('items.base_code')
                                    ->where('items.base_code', '!=', '')
                                    ->whereExists(function ($sub) {
                                        $sub->select(DB::raw(1))
                                            ->from('items as i2')
                                            ->whereColumn('i2.id', 'di.item_id')
                                            ->whereColumn('i2.base_code', 'items.base_code');
                                    });
                            });
                    });
            })
            ->select(
                'items.id as item_id',
                'items.name as item_name',
                'items.code as item_code',
                'items.unit',
                'student_size_items.size',
                DB::raw('COALESCE(item_variants.size_label, student_size_items.size) as size_label'),
                DB::raw('COALESCE(item_variants.sku, CONCAT(items.code, \'-\', student_size_items.size)) as sku'),
                DB::raw('COUNT(*) as demand'),
                DB::raw('COALESCE(MAX(stock_balances.quantity), 0) as stock')
            )
            ->groupBy(
                'items.id',
                'items.name',
                'items.code',
                'items.unit',
                'student_size_items.size',
                'item_variants.size_label',
                'item_variants.sku'
            )
            ->orderBy('items.name')
            ->orderBy('student_size_items.size')
            ->get()
            ->map(function ($row) {
                $row->shortage = max(0, $row->demand - $row->stock);
                if ($row->stock <= 0 && $row->demand > 0) {
                    $row->status = 'out_of_stock';
                } elseif ($row->shortage > 0) {
                    $row->status = 'shortage';
                } elseif ($row->demand > 0 && $row->stock >= $row->demand) {
                    $row->status = 'fulfilled';
                } else {
                    $row->status = 'excess';
                }

                return $row;
            });
    }

    public function getOutOfStockItems(): Collection
    {
        $balances = StockBalance::with(['item.category', 'variant'])
            ->where('quantity', '<=', 0)
            ->orderBy('item_id')
            ->get();

        $demands = $this->getPendingDemandCounts();

        return $balances->map(function (StockBalance $balance) use ($demands) {
            $balance->demand = $demands[$balance->item_id.'|'.$balance->variant?->size] ?? 0;

            return $balance;
        });
    }

    private function generateReferenceNumber(): string
    {
        $dateStr = date('Ymd');
        $seq = $this->nextSequence('SR', now()->toDateString());

        return 'SR-'.$dateStr.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function nextSequence(string $type, string $period): int
    {
        return $this->nextSequenceBlock($type, $period, 1);
    }

    /**
     * Reserve a contiguous block of document numbers and return the first one.
     */
    public function nextSequenceBlock(string $type, string $period, int $count): int
    {
        if ($count < 1) {
            return $this->nextSequenceBlock($type, $period, 1);
        }

        return DB::transaction(function () use ($type, $period, $count) {
            DB::table('document_sequences')->insertOrIgnore([
                'type' => $type,
                'period' => $period,
                'value' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $row = DB::table('document_sequences')
                ->where('type', $type)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            $first = (int) $row->value + 1;

            DB::table('document_sequences')
                ->where('id', $row->id)
                ->update(['value' => $first + $count - 1, 'updated_at' => now()]);

            return $first;
        });
    }

    public function reverseStockReceive(StockReceive $receive): void
    {
        DB::transaction(function () use ($receive) {
            $receive = StockReceive::whereKey($receive->id)->lockForUpdate()->first();

            if (! $receive) {
                throw new \Exception('Penerimaan tidak ditemukan.');
            }

            $items = $receive->items()->get();
            $itemIds = $items->pluck('id')->all();

            $itemMasterIds = $items->pluck('item_id')->unique()->all();
            $names = null;
            $itemName = function (StockReceiveItem $item) use (&$names, $itemMasterIds): string {
                $names ??= Item::whereIn('id', $itemMasterIds)->pluck('name', 'id')->all();

                return $names[$item->item_id] ?? 'Barang';
            };

            $remainingByItem = StockBatch::whereIn('stock_receive_item_id', $itemIds)
                ->selectRaw('stock_receive_item_id, SUM(quantity_remaining) as qty')
                ->groupBy('stock_receive_item_id')
                ->pluck('qty', 'stock_receive_item_id');

            foreach ($items as $item) {
                if ((int) ($remainingByItem[$item->id] ?? 0) < (int) $item->quantity) {
                    throw new \Exception(
                        "Penerimaan {$itemName($item)} sudah ada distribusi/pengeluaran stok. "
                        .'Gunakan Stock Opname untuk koreksi stok.'
                    );
                }
            }

            $balances = StockBalance::whereIn('item_id', $items->pluck('item_id')->unique()->all())
                ->whereIn('variant_id', $items->pluck('variant_id')->unique()->all())
                ->lockForUpdate()
                ->get()
                ->keyBy(fn (StockBalance $balance) => $balance->item_id.'-'.$balance->variant_id);

            $newQuantities = [];
            foreach ($items as $item) {
                $balance = $balances[$item->item_id.'-'.$item->variant_id] ?? null;

                if (! $balance) {
                    continue;
                }

                if ($balance->quantity < $item->quantity) {
                    throw new \Exception(
                        "Saldo stok tidak mencukupi untuk membatalkan penerimaan {$itemName($item)} "
                        ."(stok saat ini {$balance->quantity}, dibutuhkan {$item->quantity}). "
                        .'Gunakan Stock Opname untuk koreksi stok.'
                    );
                }

                $newQuantities[$balance->id] = (int) $balance->quantity - (int) $item->quantity;
            }

            StockMovement::where('reference_type', StockReceive::class)
                ->where('reference_id', $receive->id)
                ->delete();

            StockBatch::whereIn('stock_receive_item_id', $itemIds)->delete();

            if ($newQuantities !== []) {
                StockBalance::whereIn('id', array_keys($newQuantities))->update([
                    'quantity' => DB::raw($this->caseWhenSql('id', $newQuantities)),
                    'updated_at' => now(),
                ]);
            }

            $receive->items()->delete();
            $receive->forceDelete();

            $this->forgetDashboardStockCache();
        }, attempts: 5);
    }

    public function returnStockToBatch(int $itemId, int $variantId, int $quantity, string $referenceType, int $referenceId, ?string $notes = null): void
    {
        DB::transaction(function () use ($itemId, $variantId, $quantity, $referenceType, $referenceId, $notes) {
            $movements = StockMovement::where('item_id', $itemId)
                ->where('variant_id', $variantId)
                ->whereIn('type', ['IN', 'OUT'])
                ->whereNotNull('stock_batch_id')
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->lockForUpdate()
                ->get();

            $outMovements = $movements->where('type', 'OUT');
            $alreadyReturned = $movements->where('type', 'IN')
                ->groupBy('stock_batch_id')
                ->map(fn (Collection $rows) => (int) $rows->sum('quantity'))
                ->all();

            $batches = StockBatch::whereIn('id', $outMovements->pluck('stock_batch_id')->unique()->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $returnQty = $quantity;
            $totalReturnCost = 0.0;
            $batchIncrements = [];
            $movementRows = [];
            $timestamp = now();

            foreach ($outMovements as $movement) {
                if ($returnQty <= 0) {
                    break;
                }

                $availableToReturn = max(0, (int) $movement->quantity - ($alreadyReturned[$movement->stock_batch_id] ?? 0));

                if ($availableToReturn <= 0) {
                    continue;
                }

                $batch = $batches[$movement->stock_batch_id] ?? null;
                if (! $batch) {
                    continue;
                }

                $returnToBatch = min($returnQty, $availableToReturn);
                $batchIncrements[$batch->id] = ($batchIncrements[$batch->id] ?? 0) + $returnToBatch;
                $totalReturnCost += $returnToBatch * (float) $batch->unit_hpp;

                $movementRows[] = [
                    'item_id' => $itemId,
                    'variant_id' => $variantId,
                    'type' => 'IN',
                    'quantity' => $returnToBatch,
                    'hpp' => $batch->unit_hpp,
                    'stock_batch_id' => $batch->id,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'notes' => $notes ?? 'Retur barang ke batch asal',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];

                $returnQty -= $returnToBatch;
            }

            if ($returnQty > 0) {
                throw new \Exception("Tidak dapat mengembalikan {$returnQty} unit: batch asal tidak ditemukan.");
            }

            $batchQuantities = [];
            foreach ($batchIncrements as $batchId => $increment) {
                $batchQuantities[$batchId] = (int) $batches[$batchId]->quantity_remaining + $increment;
            }

            if ($batchQuantities !== []) {
                StockBatch::whereIn('id', array_keys($batchQuantities))->update([
                    'quantity_remaining' => DB::raw($this->caseWhenSql('id', $batchQuantities)),
                    'updated_at' => $timestamp,
                ]);
            }

            if ($movementRows !== []) {
                StockMovement::insert($movementRows);
            }

            $returnedHpp = $quantity > 0 ? round($totalReturnCost / $quantity, 2) : 0;

            $this->increaseBalanceLocked($itemId, $variantId, $quantity, $returnedHpp);
        }, attempts: 5);

        $this->forgetDashboardStockCache();
    }

    public function getVendorReceivedItems(int $vendorId): Collection
    {
        return StockReceive::with(['items.item', 'items.variant'])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->get();
    }
}
