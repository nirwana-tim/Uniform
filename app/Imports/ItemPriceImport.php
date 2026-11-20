<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemPrice;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;

class ItemPriceImport implements ToCollection, WithHeadingRow
{
    private int $importedCount = 0;

    private int $totalRows = 0;

    public function headingRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows): void
    {
        $records = $this->recordsFromRows($rows);
        $this->totalRows = count($records);

        $failures = $this->validateRecords($records);
        if ($failures !== []) {
            throw new ValidationException(
                IlluminateValidationException::withMessages([]),
                $failures
            );
        }

        DB::transaction(function () use ($records) {
            $codes = array_values(array_unique(array_filter(array_column($records, 'kode_barang'))));

            if ($codes === []) {
                return;
            }

            $itemIds = Item::query()->whereIn('code', $codes)->pluck('id', 'code');

            $now = now();
            $rows = [];

            foreach ($records as $record) {
                $itemId = $itemIds[$record['kode_barang']] ?? null;
                if (! $itemId) {
                    continue;
                }

                $effectiveDate = $this->resolveEffectiveDate($record['tahun_akademik'] ?? null);

                $rows[] = [
                    'item_id' => $itemId,
                    'effective_date' => $effectiveDate,
                    'selling_price' => $record['harga_jual'],
                    'hpp' => $record['hpp'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $this->importedCount++;
            }

            if ($rows !== []) {
                ItemPrice::upsert(
                    $rows,
                    ['item_id', 'effective_date'],
                    ['selling_price', 'hpp', 'updated_at']
                );
            }
        });
    }

    private function resolveEffectiveDate(?string $tahunAkademik): string
    {
        if (! $tahunAkademik) {
            return now()->startOfYear()->toDateString();
        }

        if (preg_match('/^(\d{2,4})\s*\/\s*(\d{2})$/', $tahunAkademik, $matches)) {
            $year = (int) $matches[1];
            $year = $year < 100 ? 2000 + $year : $year;

            return "{$year}-07-01";
        }

        $timestamp = strtotime($tahunAkademik);

        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return now()->startOfYear()->toDateString();
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function countRows(Collection $rows): int
    {
        return count($this->recordsFromRows($rows));
    }

    private function recordsFromRows(Collection $rows): array
    {
        $records = [];

        foreach ($rows as $index => $row) {
            $values = $row instanceof Collection ? $row->toArray() : (array) $row;

            $kodeBarang = $this->clean($values['kode_barang'] ?? null);
            $hargaJual = $this->parseDecimal($values['harga_jual'] ?? $values['harga_jual_rp'] ?? null);
            $hpp = $this->parseDecimal($values['hpp'] ?? $values['hpp_rp'] ?? null);

            if ($kodeBarang === null && $hargaJual === null && $hpp === null) {
                continue;
            }

            $records[] = [
                'row' => $index + 1,
                'kode_barang' => $kodeBarang,
                'tahun_akademik' => $this->clean($values['tahun_akademik'] ?? null),
                'harga_jual' => $hargaJual,
                'hpp' => $hpp,
            ];
        }

        return $records;
    }

    private function validateRecords(array &$records): array
    {
        $failures = [];
        $codes = array_filter(array_map(fn ($r) => $r['kode_barang'], $records));
        $validCodes = $codes !== [] ? Item::whereIn('code', $codes)->pluck('code')->flip() : collect();

        foreach ($records as &$record) {
            $rules = [
                'kode_barang' => ['required', 'string', 'max:100'],
                'harga_jual' => ['required', 'numeric', 'min:0'],
                'hpp' => ['required', 'numeric', 'min:0'],
            ];

            $validator = Validator::make($record, $rules);

            if ($record['kode_barang'] !== null && ! $validCodes->has($record['kode_barang'])) {
                $validator->errors()->add('kode_barang', 'Kode barang tidak ditemukan di database.');
            }

            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $failures[] = new Failure($record['row'], $attribute, $messages, $record);
            }
        }

        return $failures;
    }

    private function clean(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_numeric($value) && (str_contains(strtolower((string) $value), 'e+') || is_float($value))) {
            $value = number_format((float) $value, 0, '', '');
        }
        $value = ltrim(trim((string) $value), "'");

        return $value === '' ? null : $value;
    }

    private function parseDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $text = trim((string) preg_replace('/^Rp\.?\s*/i', '', trim((string) $value)));
        $isNegative = str_starts_with($text, '-');
        $unsigned = trim((string) preg_replace('/^Rp\.?\s*/i', '', ltrim($text, '+-')));

        if (str_contains($unsigned, ',')) {
            $lastComma = strrpos($unsigned, ',');
            $lastDot = strrpos($unsigned, '.');
            if ($lastDot === false || $lastComma > $lastDot) {
                $unsigned = str_replace('.', '', $unsigned);
                $unsigned = str_replace(',', '.', $unsigned);
            } else {
                $unsigned = str_replace(',', '', $unsigned);
            }
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $unsigned)) {
            $unsigned = str_replace('.', '', $unsigned);
        }

        $cleaned = preg_replace('/[^0-9.]/', '', $unsigned);

        if ($cleaned === null || $cleaned === '') {
            return null;
        }

        return $isNegative ? -(float) $cleaned : (float) $cleaned;
    }
}
