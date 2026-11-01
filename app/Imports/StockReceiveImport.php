<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\StockReceive;
use App\Models\Vendor;
use App\Services\StockService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StockReceiveImport implements ToCollection, WithHeadingRow, WithMultipleSheets
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

        $groups = $this->groupRecords($records);
        $groups = $this->resolveGroups($groups);
        $stockService = app(StockService::class);
        $vendors = $this->resolveVendors($groups);

        $existingRefs = StockReceive::whereIn('reference_number', array_keys($groups))
            ->pluck('reference_number')
            ->flip();

        if ($existingRefs->isNotEmpty()) {
            $failures = [];
            foreach ($records as $record) {
                if ($record['nomor_ref'] && $existingRefs->has($record['nomor_ref'])) {
                    $failures[] = new Failure(
                        $record['row'],
                        'nomor_ref',
                        ['reference_number sudah terdaftar.'],
                        $record
                    );
                }
            }

            if ($failures !== []) {
                throw new ValidationException(
                    IlluminateValidationException::withMessages([]),
                    $failures
                );
            }
        }

        foreach ($groups as $group) {
            $vendor = $vendors[trim($group['vendor_name'])];

            $data = [
                'vendor_id' => $vendor->id,
                'receive_date' => $group['receive_date'],
                'reference_number' => $group['reference_number'],
                'notes' => $group['notes'],
                'items' => $group['resolved_items'],
            ];

            $stockService->receiveStock($data);
            $this->importedCount += count($data['items']);
        }
    }

    public function sheets(): array
    {
        $parent = $this;

        return ['Data' => new class($parent) implements ToCollection, WithHeadingRow
        {
            private StockReceiveImport $parent;

            public function __construct(StockReceiveImport $parent)
            {
                $this->parent = $parent;
            }

            public function headingRow(): int
            {
                return 4;
            }

            public function collection(Collection $rows): void
            {
                $this->parent->collection($rows);
            }
        }];
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

            if ($this->shouldSkipRow($values)) {
                continue;
            }

            $rawDate = $values['tanggal'] ?? $values['receive_date'] ?? $values['tanggal_terima'] ?? $values['tgl_terima'] ?? null;

            $records[] = [
                'row' => $index + 1,
                'kode_barang' => $this->clean($values['kode_barang'] ?? null),
                'sku' => $this->clean($values['sku'] ?? $values['sku_varian'] ?? $values['varian_sku'] ?? null),
                'quantity' => $this->parseNumeric($values['quantity'] ?? $values['qty'] ?? null),
                'unit_price' => $this->parseDecimal($values['unit_price'] ?? $values['harga_satuan'] ?? $values['harga_satuan_rp'] ?? null),
                'hpp' => $this->parseDecimal($values['hpp'] ?? $values['hpp_rp'] ?? null),
                'vendor' => $this->clean($values['vendor'] ?? $values['vendor_name'] ?? $values['nama_vendor'] ?? null),
                'tanggal' => $this->parseDate($rawDate) ?? (blank($rawDate) ? now()->toDateString() : null),
                'nomor_ref' => $this->clean($values['nomor_ref'] ?? $values['reference_number'] ?? null),
                'notes' => $this->clean($values['notes'] ?? $values['keterangan'] ?? null),
            ];
        }

        return $records;
    }

    private function validateRecords(array &$records): array
    {
        $failures = [];

        foreach ($records as &$record) {
            $rules = [
                'kode_barang' => ['required', 'string', 'max:100'],
                'quantity' => ['required', 'integer', 'min:1'],
                'vendor' => ['required', 'string', 'max:255'],
                'tanggal' => ['required', 'string', 'date_format:Y-m-d'],
            ];

            $validator = Validator::make($record, $rules);

            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $failures[] = new Failure($record['row'], $attribute, $messages, $record);
            }
        }

        return $failures;
    }

    private function groupRecords(array $records): array
    {
        $groups = [];
        $stockService = app(StockService::class);

        $pendingPerDate = [];
        foreach ($records as $record) {
            if (! $record['nomor_ref']) {
                $date = $record['tanggal'] ?? now()->toDateString();
                $pendingPerDate[$date] = ($pendingPerDate[$date] ?? 0) + 1;
            }
        }

        $nextSequence = [];
        foreach ($pendingPerDate as $date => $count) {
            $nextSequence[$date] = $stockService->nextSequenceBlock('SR', $date, $count);
        }

        foreach ($records as $record) {
            $date = $record['tanggal'] ?? now()->toDateString();
            $ref = $record['nomor_ref'];

            if (! $ref) {
                $sequence = $nextSequence[$date]++;
                $dateString = str_replace('-', '', $date);
                $ref = 'SR-'.$dateString.'-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            }

            $key = $ref;

            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'vendor_name' => $record['vendor'],
                    'receive_date' => $record['tanggal'],
                    'reference_number' => $ref,
                    'notes' => $record['notes'],
                    'items' => [],
                ];
            }

            $groups[$key]['items'][] = $record;
        }

        return $groups;
    }

    /**
     * @return array<string, Vendor>
     */
    private function resolveVendors(array $groups): array
    {
        $names = [];

        foreach ($groups as $group) {
            $names[trim($group['vendor_name'])] = true;
        }

        $vendors = Vendor::query()
            ->whereIn('name', array_keys($names))
            ->get()
            ->keyBy('name')
            ->all();

        $now = now();
        $missing = [];

        foreach (array_keys($names) as $name) {
            if (! isset($vendors[$name])) {
                $missing[] = ['name' => $name, 'created_at' => $now, 'updated_at' => $now];
            }
        }

        if ($missing !== []) {
            Vendor::insert($missing);

            $vendors = Vendor::query()
                ->whereIn('name', array_keys($names))
                ->get()
                ->keyBy('name')
                ->all();
        }

        return $vendors;
    }

    private function resolveGroups(array $groups): array
    {
        $failures = [];
        $codes = [];

        foreach ($groups as $group) {
            foreach ($group['items'] as $itemData) {
                $codes[$itemData['kode_barang']] = true;
            }
        }

        $index = [];
        $items = Item::with('variants')
            ->where(function ($q) use ($codes) {
                $q->whereIn('code', array_keys($codes))
                    ->orWhereIn('base_code', array_keys($codes));
            })
            ->get();

        foreach ($items as $item) {
            $index[$item->code][] = $item;

            if ($item->base_code && $item->base_code !== $item->code) {
                $index[$item->base_code][] = $item;
            }
        }

        foreach ($groups as $key => $group) {
            $resolvedItems = [];

            foreach ($group['items'] as $itemData) {
                $matches = $index[$itemData['kode_barang']] ?? [];

                if ($matches === []) {
                    $failures[] = new Failure(
                        $itemData['row'],
                        'kode_barang',
                        ['Barang dengan kode ini tidak ditemukan.'],
                        $itemData
                    );

                    continue;
                }

                if (count($matches) > 1) {
                    $failures[] = new Failure(
                        $itemData['row'],
                        'kode_barang',
                        ['Kode barang cocok dengan lebih dari satu item. Gunakan kode barang yang spesifik.'],
                        $itemData
                    );

                    continue;
                }

                $item = $matches[0];
                $variants = $item->variants;

                if ($itemData['sku']) {
                    $variant = $variants->firstWhere('sku', $itemData['sku']);

                    if (! $variant) {
                        $failures[] = new Failure(
                            $itemData['row'],
                            'sku',
                            ['SKU varian tidak ditemukan untuk barang ini.'],
                            $itemData
                        );

                        continue;
                    }
                } elseif ($variants->count() > 1) {
                    $failures[] = new Failure(
                        $itemData['row'],
                        'sku',
                        ['SKU wajib diisi karena barang memiliki lebih dari satu varian ukuran.'],
                        $itemData
                    );

                    continue;
                } else {
                    $variant = $variants->first();

                    if (! $variant) {
                        $failures[] = new Failure(
                            $itemData['row'],
                            'sku',
                            ['Barang belum memiliki varian ukuran.'],
                            $itemData
                        );

                        continue;
                    }
                }

                $resolvedItems[] = [
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'hpp' => $itemData['hpp'],
                ];
            }

            $groups[$key]['resolved_items'] = $resolvedItems;
        }

        if ($failures !== []) {
            throw new ValidationException(
                IlluminateValidationException::withMessages([]),
                $failures
            );
        }

        return $groups;
    }

    private function shouldSkipRow(array $values): bool
    {
        $firstVal = $this->clean(reset($values));
        if ($firstVal === null) {
            return collect($values)->filter(fn ($v) => $this->clean($v) !== null)->isEmpty();
        }

        return Str::startsWith(Str::upper($firstVal), [
            'TEMPLATE IMPORT', 'ISI DATA', 'URUTAN KOLOM',
            'KODE BARANG', 'CONTOH FORMAT', 'CONTOH',
        ]);
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

    private function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateString();
        }

        if (is_numeric($value) && (float) $value > 0) {
            try {
                return Carbon::instance(Date::excelToDateTimeObject((float) $value))->toDateString();
            } catch (\Throwable) {
                // lanjut ke parsing string
            }
        }

        $text = trim((string) $value);

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'd/m/y', 'd-m-y'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $text);

                if ($date !== false) {
                    return $date->toDateString();
                }
            } catch (\Throwable) {
                // coba format berikutnya
            }
        }

        try {
            return Carbon::parse($text)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseNumeric(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        $cleaned = preg_replace('/[^0-9]/', '', (string) $value);

        return $cleaned !== '' ? (int) $cleaned : null;
    }

    private function parseDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }

        $text = trim((string) $value);

        if (str_contains($text, ',')) {
            $lastComma = strrpos($text, ',');
            $lastDot = strrpos($text, '.');
            if ($lastComma > $lastDot) {
                $text = str_replace('.', '', $text);
                $text = str_replace(',', '.', $text);
            } else {
                $text = str_replace(',', '', $text);
            }
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $text)) {
            $text = str_replace('.', '', $text);
        }

        $cleaned = preg_replace('/[^0-9.]/', '', $text);

        return $cleaned !== '' ? (float) $cleaned : null;
    }
}
