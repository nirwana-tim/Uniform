<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemDepartment;
use App\Models\ItemPrice;
use App\Models\ItemSize;
use App\Models\ItemType;
use App\Models\ItemVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;

class ItemImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    private int $importedCount = 0;

    private int $totalRows = 0;

    private array $sizeCodes = [];

    public function headingRow(): int
    {
        return 4;
    }

    public function __construct()
    {
        $this->sizeCodes = ItemSize::pluck('code', 'label')->toArray();
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

        if ($records === []) {
            return;
        }

        DB::transaction(function () use ($records) {
            $categoryByLabel = ItemCategory::query()->get()->keyBy('label')->all();
            $categoryByCode = ItemCategory::query()->get()->keyBy('code')->all();
            $typeByCode = ItemType::query()->get()->keyBy(fn (ItemType $type): string => strtoupper((string) $type->code))->all();
            $typeByLabel = ItemType::query()->get()->keyBy('label')->all();
            $departmentByLabel = ItemDepartment::query()->get()->keyBy('label')->all();
            $departmentByCode = ItemDepartment::query()->get()->keyBy('code')->all();

            $now = now();
            $pending = [];

            foreach ($records as $record) {
                $category = $this->resolveCategory($record['kategori'], $categoryByLabel, $categoryByCode);
                $department = $this->resolveDepartment($record['departemen'], $departmentByLabel, $departmentByCode);
                $type = $this->resolveType($record['type'], $typeByCode, $typeByLabel);

                $baseCode = $this->generateBaseCode($category, $record['gender'], $type, $department);
                $code = $record['kode'] ?: $baseCode;

                if (isset($pending[$code])) {
                    $pending[$code]['record']['sizes'] = array_merge(
                        $pending[$code]['record']['sizes'],
                        $record['sizes']
                    );

                    continue;
                }

                $pending[$code] = [
                    'record' => $record,
                    'base_code' => $baseCode,
                    'category' => $category,
                    'type' => $type,
                    'department' => $department,
                ];
            }

            $existingCodes = Item::query()
                ->whereIn('code', array_keys($pending))
                ->pluck('code')
                ->flip();

            $newItems = [];

            foreach ($pending as $code => $info) {
                if ($existingCodes->has($code)) {
                    continue;
                }

                $record = $info['record'];

                $newItems[] = [
                    'name' => $record['nama'],
                    'code' => $code,
                    'base_code' => $info['base_code'],
                    'gender' => $record['gender'],
                    'category_id' => $info['category']?->id,
                    'type_id' => $info['type']?->id,
                    'department_id' => $info['department']?->id,
                    'unit' => $record['satuan'],
                    'selling_price' => $record['harga_jual'] ?? 0,
                    'hpp' => $record['hpp'] ?? 0,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($newItems !== []) {
                Item::insert($newItems);
            }

            $itemIds = Item::query()
                ->whereIn('code', array_keys($pending))
                ->pluck('id', 'code');

            $priceRows = [];
            $variantRows = [];
            $skus = [];

            foreach ($pending as $code => $info) {
                $itemId = $itemIds[$code] ?? null;

                if (! $itemId) {
                    continue;
                }

                $record = $info['record'];

                if (! empty($record['harga_jual']) || ! empty($record['hpp'])) {
                    $priceRows[] = [
                        'item_id' => $itemId,
                        'effective_date' => now()->startOfYear()->toDateString(),
                        'selling_price' => $record['harga_jual'] ?? 0,
                        'hpp' => $record['hpp'] ?? 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $itemSkus = array_map(fn (string $sizeCode): string => $code.'-'.$sizeCode, $this->sizeCodes);
                $itemSkus[] = $code.'-01';
                $skus = array_merge($skus, $itemSkus);

                $variantCreated = false;

                foreach ($this->sizeCodes as $sizeLabel => $sizeCode) {
                    if (($record['sizes'][$sizeLabel] ?? null) === null) {
                        continue;
                    }

                    $variantRows[] = [
                        'item_id' => $itemId,
                        'size' => $sizeCode,
                        'size_label' => $sizeLabel,
                        'sku' => $code.'-'.$sizeCode,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $variantCreated = true;
                }

                if (! $variantCreated) {
                    $variantRows[] = [
                        'item_id' => $itemId,
                        'size' => '01',
                        'size_label' => 'All Size',
                        'sku' => $code.'-01',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if ($priceRows !== []) {
                ItemPrice::upsert(
                    $priceRows,
                    ['item_id', 'effective_date'],
                    ['selling_price', 'hpp', 'updated_at']
                );
            }

            if ($skus !== []) {
                ItemVariant::whereNull('item_id')
                    ->whereIn('sku', array_values(array_unique($skus)))
                    ->delete();
            }

            if ($variantRows !== []) {
                ItemVariant::upsert(
                    $variantRows,
                    ['sku'],
                    ['item_id', 'size', 'size_label', 'updated_at']
                );
            }

            $this->importedCount = count($records);
        });
    }

    public function sheets(): array
    {
        $parent = $this;

        return ['Data' => new class($parent) implements ToCollection, WithHeadingRow
        {
            private ItemImport $parent;

            public function __construct(ItemImport $parent)
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
        $allLabels = array_keys($this->sizeCodes);

        foreach ($rows as $index => $row) {
            $values = $row instanceof Collection ? $row->toArray() : (array) $row;

            if ($this->shouldSkipRow($values)) {
                continue;
            }

            $record = [
                'row' => $index + 1,
                'kategori' => $this->clean($values['kategori'] ?? null),
                'gender' => strtoupper($this->clean($values['gender'] ?? null) ?? ''),
                'nama' => $this->clean($values['nama_item'] ?? $values['nama'] ?? null),
                'kode' => $this->clean($values['kode_barang'] ?? $values['kode'] ?? null),
                'type' => strtoupper($this->clean($values['type'] ?? null) ?? ''),
                'departemen' => $this->clean($values['departemen'] ?? null),
                'satuan' => $this->clean($values['satuan'] ?? null),
                'harga_jual' => $this->parseNumeric($values['harga_jual'] ?? $values['harga_jual_rp'] ?? null),
                'hpp' => $this->parseNumeric($values['hpp'] ?? $values['hpp_rp'] ?? null),
                'sizes' => [],
            ];

            foreach ($allLabels as $label) {
                $labelLower = strtolower($label);
                $labelSlug = Str::slug($label, '_');
                $found = false;
                foreach ($values as $key => $val) {
                    $keyLower = strtolower((string) $key);
                    if ($keyLower === $labelLower || $keyLower === $labelSlug) {
                        $parsed = $this->parseNumeric($val);
                        if ($parsed !== null) {
                            $record['sizes'][$label] = $parsed;
                        }
                        $found = true;
                        break;
                    }
                }
                if (! $found && isset($values[$label])) {
                    $parsed = $this->parseNumeric($values[$label]);
                    if ($parsed !== null) {
                        $record['sizes'][$label] = $parsed;
                    }
                }
            }

            $records[] = $record;
        }

        return $records;
    }

    private function validateRecords(array &$records): array
    {
        $failures = [];
        $seenCodes = [];

        foreach ($records as &$record) {
            $rules = [
                'nama' => ['required', 'string', 'max:255'],
                'kategori' => ['required', 'string', 'max:255'],
                'gender' => ['required', 'string', 'in:L,P,U'],
                'type' => ['required', 'string', 'max:10'],
                'satuan' => ['required', 'string', 'max:50'],
            ];

            $validator = Validator::make($record, $rules);

            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $failures[] = new Failure($record['row'], $attribute, $messages, $record);
            }

            if ($record['kode'] && isset($seenCodes[$record['kode']])) {
                $failures[] = new Failure($record['row'], 'kode', ['Duplicate item code in this file.'], $record);
            }
            if ($record['kode']) {
                $seenCodes[$record['kode']] = true;
            }

            $record['kategori'] = strtoupper($record['kategori']);
        }

        return $failures;
    }

    private function generateBaseCode(?ItemCategory $category, string $gender, ?ItemType $type, ?ItemDepartment $department): string
    {
        return implode('-', [
            $category?->code ?? 'GEN',
            $gender ?: 'U',
            $type?->code ?? 'XX',
            $department?->code ?? '00',
        ]);
    }

    /**
     * @param  array<string, ItemType>  $byCode
     * @param  array<string, ItemType>  $byLabel
     */
    private function resolveType(?string $value, array $byCode, array $byLabel): ?ItemType
    {
        if (! $value) {
            return null;
        }

        return $byCode[strtoupper($value)] ?? $byLabel[$value] ?? null;
    }

    /**
     * @param  array<string, ItemCategory>  $byLabel
     * @param  array<string, ItemCategory>  $byCode
     */
    private function resolveCategory(string $name, array &$byLabel, array &$byCode): ?ItemCategory
    {
        $code = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 3));

        if (isset($byLabel[$name])) {
            return $byLabel[$name];
        }

        if (isset($byCode[$code])) {
            return $byCode[$code];
        }

        $baseCode = $code;
        $suffix = 1;
        while (isset($byCode[$baseCode])) {
            $baseCode = $code.$suffix++;
        }

        $category = ItemCategory::create(['label' => $name, 'code' => $baseCode]);

        $byLabel[$name] = $category;
        $byCode[$baseCode] = $category;

        return $category;
    }

    /**
     * @param  array<string, ItemDepartment>  $byLabel
     * @param  array<string, ItemDepartment>  $byCode
     */
    private function resolveDepartment(string $name, array $byLabel, array $byCode): ?ItemDepartment
    {
        if (! $name) {
            return null;
        }

        return $byLabel[$name] ?? $byCode[$name] ?? null;
    }

    private function shouldSkipRow(array $values): bool
    {
        $firstKey = array_key_first($values);
        $firstVal = $this->clean($values[$firstKey] ?? null);

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
}
