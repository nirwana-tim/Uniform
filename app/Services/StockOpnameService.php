<?php

namespace App\Services;

use App\Models\ItemPrice;
use App\Models\StockBalance;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameAdjustment;
use App\Models\StockOpnameItem;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    public function __construct(
        private readonly StockService $stockService
    ) {}

    public function createBatch(array $data): StockOpname
    {
        $period = $data['period'];
        $seq = $this->stockService->nextSequence('SO', $period);

        $referenceNumber = 'SO-'.$period.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

        $batch = StockOpname::create([
            'reference_number' => $referenceNumber,
            'opname_date' => $data['opname_date'],
            'period' => $data['period'],
            'notes' => $data['notes'] ?? null,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        return $batch;
    }

    public function calculateVariance(StockOpname $batch): void
    {
        DB::transaction(function () use ($batch) {
            $items = $batch->items()->get();

            if ($items->isEmpty()) {
                return;
            }

            $now = now();
            $rows = $items->map(fn (StockOpnameItem $item): array => [
                'id' => $item->id,
                'stock_opname_id' => $item->stock_opname_id,
                'item_id' => $item->item_id,
                'variant_id' => $item->variant_id,
                'system_quantity' => $item->system_quantity,
                'physical_quantity' => $item->physical_quantity,
                'notes' => $item->variance != 0
                    ? ($item->variance > 0 ? 'Surplus: +'.$item->variance : 'Shortage: '.$item->variance)
                    : 'Sesuai',
                'created_at' => $item->created_at ?? $now,
                'updated_at' => $now,
            ])->all();

            StockOpnameItem::upsert($rows, ['id'], ['notes', 'updated_at']);
        });
    }

    public function createAdjustments(StockOpname $batch, User $approver): void
    {
        DB::transaction(function () use ($batch, $approver) {
            $batch = StockOpname::whereKey($batch->id)->lockForUpdate()->first();
            if (! $batch || $batch->status !== 'counted') {
                throw new \Exception('Stock opname sudah di-approve atau tidak dalam status counted.');
            }

            $batch->load('items.item', 'items.variant');

            if ($batch->items->isEmpty()) {
                throw new \Exception('Stock opname tidak memiliki item. Silakan upload data terlebih dahulu.');
            }

            $items = $batch->items;
            $itemIds = $items->pluck('item_id')->filter()->unique()->values()->all();

            $balances = StockBalance::query()
                ->whereIn('item_id', $itemIds)
                ->lockForUpdate()
                ->get()
                ->keyBy(fn (StockBalance $balance): string => $balance->item_id.'-'.$balance->variant_id);

            $fallbackHpp = ItemPrice::query()
                ->whereIn('item_id', $itemIds)
                ->orderByDesc('effective_date')
                ->orderByDesc('id')
                ->get()
                ->groupBy('item_id')
                ->map(fn ($group): float => (float) $group->first()->hpp);

            $now = now();
            $runningQuantities = [];
            $runningHpps = [];
            $systemQuantities = [];
            $notes = [];
            $surplusBalances = [];
            $surplusRows = [];
            $surplusMeta = [];
            $adjustmentRows = [];
            $fifoRequests = [];

            foreach ($items as $item) {
                $key = $item->item_id.'-'.$item->variant_id;
                $balance = $balances->get($key);
                $available = $runningQuantities[$key] ?? (int) ($balance?->quantity ?? 0);

                $systemQuantities[$item->id] = $available;

                if ($item->variance == 0) {
                    continue;
                }

                if ($item->variance > 0) {
                    $quantity = (int) $item->variance;
                    $unitHpp = (float) ($runningHpps[$key] ?? $balance?->last_hpp ?? 0);

                    if ($unitHpp <= 0) {
                        $unitHpp = (float) ($item->item?->hpp ?? $fallbackHpp[$item->item_id] ?? 0);
                    }

                    $surplusRows[] = [
                        'item_id' => $item->item_id,
                        'variant_id' => $item->variant_id,
                        'quantity_remaining' => $quantity,
                        'unit_hpp' => $unitHpp,
                        'received_date' => today()->toDateString(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $surplusMeta[] = [
                        'item_id' => $item->item_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $quantity,
                        'unit_hpp' => $unitHpp,
                    ];

                    $newQuantity = $available + $quantity;
                    $oldQuantity = $available;
                    $oldHpp = (float) ($runningHpps[$key] ?? $balance?->last_hpp ?? 0);
                    $newHpp = $newQuantity > 0
                        ? ((($oldQuantity * $oldHpp) + ($quantity * $unitHpp)) / $newQuantity)
                        : $unitHpp;

                    $runningHpps[$key] = round($newHpp, 2);

                    $surplusBalances[$key] = [
                        'balance' => $balance,
                        'item_id' => $item->item_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $newQuantity,
                        'last_hpp' => round($newHpp, 2),
                    ];
                    $runningQuantities[$key] = $newQuantity;

                    continue;
                }

                $shortage = abs((int) $item->variance);

                if ($available <= 0) {
                    $notes[$item->id] = 'Shortage: '.$shortage.' (stok sudah 0, tidak ada adjustment)';

                    continue;
                }

                $quantity = $shortage;

                if ($quantity > $available) {
                    $notes[$item->id] = 'Shortage: '.$shortage.' (diselaraskan ke stok tersedia '.$available.')';
                    $quantity = $available;
                }

                $fifoRequests[] = [
                    'item_id' => $item->item_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $quantity,
                ];
                $runningQuantities[$key] = $available - $quantity;
            }

            if ($surplusRows !== []) {
                $maxBatchId = (int) StockBatch::max('id');
                StockBatch::insert($surplusRows);

                $surplusItemIds = array_values(array_unique(array_map(
                    fn (array $meta): int => (int) $meta['item_id'],
                    $surplusMeta
                )));

                $newBatches = StockBatch::query()
                    ->where('id', '>', $maxBatchId)
                    ->whereIn('item_id', $surplusItemIds)
                    ->orderBy('id')
                    ->get(['id', 'item_id', 'variant_id'])
                    ->keyBy(fn (StockBatch $stockBatch): string => $stockBatch->item_id.'-'.$stockBatch->variant_id);

                $movementRows = [];
                foreach ($surplusMeta as $meta) {
                    $movementRows[] = [
                        'item_id' => $meta['item_id'],
                        'variant_id' => $meta['variant_id'],
                        'type' => 'IN',
                        'quantity' => $meta['quantity'],
                        'hpp' => $meta['unit_hpp'],
                        'stock_batch_id' => $newBatches->get($meta['item_id'].'-'.$meta['variant_id'])?->id,
                        'reference_type' => StockOpname::class,
                        'reference_id' => $batch->id,
                        'notes' => "Stock Opname adjustment (surplus) - {$batch->reference_number}",
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $maxMovementId = (int) StockMovement::max('id');
                StockMovement::insert($movementRows);

                $newMovements = StockMovement::query()
                    ->where('id', '>', $maxMovementId)
                    ->where('reference_type', StockOpname::class)
                    ->where('reference_id', $batch->id)
                    ->orderBy('id')
                    ->get(['id', 'item_id', 'variant_id'])
                    ->keyBy(fn (StockMovement $movement): string => $movement->item_id.'-'.$movement->variant_id);

                foreach ($surplusMeta as $meta) {
                    $adjustmentRows[] = [
                        'stock_opname_id' => $batch->id,
                        'stock_movement_id' => $newMovements->get($meta['item_id'].'-'.$meta['variant_id'])?->id,
                        'type' => 'surplus',
                        'quantity' => $meta['quantity'],
                        'reason' => 'Surplus',
                        'approved_by' => $approver->id,
                        'approved_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if ($fifoRequests !== []) {
                $results = $this->stockService->deductStockFifos(
                    $fifoRequests,
                    StockOpname::class,
                    $batch->id,
                    "Stock Opname adjustment (shortage) - {$batch->reference_number}"
                );

                $movementIds = collect($results)
                    ->flatMap(fn (array $result): array => $result['movement_ids'] ?? [])
                    ->unique()
                    ->values()
                    ->all();

                $movements = $movementIds === []
                    ? collect()
                    : StockMovement::query()->whereIn('id', $movementIds)->get()->keyBy('id');

                foreach ($results as $result) {
                    foreach ($result['movement_ids'] ?? [] as $movementId) {
                        $movement = $movements->get($movementId);

                        if (! $movement) {
                            continue;
                        }

                        $adjustmentRows[] = [
                            'stock_opname_id' => $batch->id,
                            'stock_movement_id' => $movement->id,
                            'type' => 'shortage',
                            'quantity' => $movement->quantity,
                            'reason' => 'Shortage',
                            'approved_by' => $approver->id,
                            'approved_at' => $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            $this->persistBalances($surplusBalances, $now);

            if ($adjustmentRows !== []) {
                StockOpnameAdjustment::insert($adjustmentRows);
            }

            $this->persistItems($items, $systemQuantities, $notes, $now);

            $batch->update([
                'status' => 'approved',
            ]);
        }, attempts: 5);
    }

    /**
     * @param  array<string, array{balance: StockBalance|null, item_id: int, variant_id: int|null, quantity: int, last_hpp: float}>  $changes
     */
    private function persistBalances(array $changes, \DateTimeInterface $now): void
    {
        if ($changes === []) {
            return;
        }

        $updates = [];
        $inserts = [];

        foreach ($changes as $change) {
            if ($change['balance']) {
                $updates[$change['balance']->id] = [
                    'quantity' => $change['quantity'],
                    'last_hpp' => $change['last_hpp'],
                ];

                continue;
            }

            $inserts[] = [
                'item_id' => $change['item_id'],
                'variant_id' => $change['variant_id'],
                'quantity' => $change['quantity'],
                'last_hpp' => $change['last_hpp'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($updates !== []) {
            $quantityCases = '';
            $hppCases = '';

            foreach ($updates as $id => $values) {
                $quantityCases .= ' WHEN '.(int) $id.' THEN '.(int) $values['quantity'];
                $hppCases .= ' WHEN '.(int) $id.' THEN '.number_format((float) $values['last_hpp'], 2, '.', '');
            }

            DB::table('stock_balances')
                ->whereIn('id', array_keys($updates))
                ->update([
                    'quantity' => DB::raw('CASE id'.$quantityCases.' END'),
                    'last_hpp' => DB::raw('CASE id'.$hppCases.' END'),
                    'updated_at' => $now,
                ]);
        }

        if ($inserts !== []) {
            StockBalance::insert($inserts);
        }
    }

    /**
     * @param  Collection<int, StockOpnameItem>  $items
     * @param  array<int, int>  $systemQuantities
     * @param  array<int, string>  $notes
     */
    private function persistItems($items, array $systemQuantities, array $notes, \DateTimeInterface $now): void
    {
        $rows = $items->map(fn (StockOpnameItem $item): array => [
            'id' => $item->id,
            'stock_opname_id' => $item->stock_opname_id,
            'item_id' => $item->item_id,
            'variant_id' => $item->variant_id,
            'system_quantity' => $systemQuantities[$item->id] ?? $item->system_quantity,
            'physical_quantity' => $item->physical_quantity,
            'notes' => $notes[$item->id] ?? $item->notes,
            'created_at' => $item->created_at ?? $now,
            'updated_at' => $now,
        ])->all();

        if ($rows === []) {
            return;
        }

        StockOpnameItem::upsert($rows, ['id'], ['system_quantity', 'notes', 'updated_at']);
    }
}
