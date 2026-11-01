<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\StockBalance;
use App\Models\StockOpnameItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StockOpnameImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected int $stockOpnameId;

    private int $importedCount = 0;

    private int $totalRows = 0;

    /** @var array<int, string>|null */
    private ?array $itemCodes = null;

    public function headingRow(): int
    {
        return 4;
    }

    public function __construct(int $stockOpnameId)
    {
        $this->stockOpnameId = $stockOpnameId;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function countRows(Collection $rows): int
    {
        return count($rows);
    }

    public function collection(Collection $rows): void
    {
        $this->totalRows = count($rows);

        if ($rows->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($rows) {
            $codes = $rows->pluck('kode_barang')->filter()->unique()->values()->all();

            $items = Item::query()
                ->with('variants')
                ->whereIn('code', $codes)
                ->get()
                ->keyBy('code');

            $balances = StockBalance::query()
                ->whereIn('item_id', $items->pluck('id'))
                ->get()
                ->keyBy(fn (StockBalance $balance): string => $balance->item_id.'-'.$balance->variant_id);

            $now = now();
            $upserts = [];

            foreach ($rows as $row) {
                $item = $items->get($row['kode_barang'] ?? null);
                if (! $item) {
                    continue;
                }

                $variantLabel = trim((string) ($row['varian_ukuran'] ?? ''));
                $variant = $item->variants->first(
                    fn ($candidate) => $candidate->size_label === $variantLabel || $candidate->size === $variantLabel
                );

                if (! $variant) {
                    continue;
                }

                $balance = $balances->get($item->id.'-'.$variant->id);

                $upserts[] = [
                    'stock_opname_id' => $this->stockOpnameId,
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                    'system_quantity' => (int) ($balance?->quantity ?? 0),
                    'physical_quantity' => (int) $row['quantity_fisik'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $this->importedCount++;
            }

            if ($upserts !== []) {
                StockOpnameItem::upsert(
                    $upserts,
                    ['stock_opname_id', 'item_id', 'variant_id'],
                    ['system_quantity', 'physical_quantity', 'updated_at']
                );
            }
        });
    }

    public function rules(): array
    {
        $this->itemCodes ??= Item::query()->pluck('code')->all();

        return [
            'kode_barang' => ['required', 'string', Rule::in($this->itemCodes)],
            'varian_ukuran' => ['required', 'string'],
            'quantity_fisik' => ['required', 'integer', 'min:0'],
        ];
    }
}
