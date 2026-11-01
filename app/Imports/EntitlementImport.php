<?php

namespace App\Imports;

use App\Models\Entitlement;
use App\Models\EntitlementItem;
use App\Models\Item;
use App\Models\Student;
use App\Models\StudentGeneration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EntitlementImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function headingRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($rows) {
            $generations = StudentGeneration::query()->get()->keyBy('name');
            $fallbackGeneration = $generations->first();

            $itemIndex = $this->buildItemIndex();

            $studentsByLevel = Student::query()
                ->whereNotNull('entitlement_code')
                ->get(['generation_id', 'student_level', 'entitlement_code'])
                ->groupBy(fn (Student $student): string => $student->generation_id.'|'.$student->student_level);

            $now = now();
            $entitlements = [];
            $entitlementItems = [];

            foreach ($rows as $row) {
                $generation = $generations->get($row['prodi_level'] ?? null) ?? $fallbackGeneration;
                if (! $generation) {
                    continue;
                }

                $studentLevel = $this->resolveStudentLevel((string) ($row['tipe'] ?? ''));

                $codes = ($studentsByLevel->get($generation->id.'|'.$studentLevel) ?? collect())
                    ->pluck('entitlement_code')
                    ->unique()
                    ->values();

                if ($codes->isEmpty()) {
                    continue;
                }

                $description = "Hak barang {$studentLevel} {$row['prodi_level']}";
                $itemColumns = collect($row)->except(['prodi_level', 'tipe']);

                foreach ($codes as $code) {
                    $key = $code.'|'.$studentLevel;

                    $entitlements[$key] = [
                        'code' => $code,
                        'student_level' => $studentLevel,
                        'description' => $description,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    foreach ($itemColumns as $itemName => $quantity) {
                        if (empty($quantity) || (int) $quantity <= 0) {
                            continue;
                        }

                        $item = $this->findItemByName((string) $itemName, $itemIndex);
                        if (! $item) {
                            continue;
                        }

                        $entitlementItems[$key.'|'.$item->id] = [
                            'code' => $code,
                            'student_level' => $studentLevel,
                            'item_id' => $item->id,
                            'quantity' => (int) $quantity,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            if ($entitlements === []) {
                return;
            }

            Entitlement::upsert(
                array_values($entitlements),
                ['code', 'student_level'],
                ['description', 'is_active', 'updated_at']
            );

            $codes = array_values(array_unique(array_column($entitlements, 'code')));

            $entitlementIds = Entitlement::query()
                ->whereIn('code', $codes)
                ->get(['id', 'code', 'student_level'])
                ->keyBy(fn (Entitlement $entitlement): string => $entitlement->code.'|'.$entitlement->student_level);

            $rowsToUpsert = [];

            foreach ($entitlementItems as $row) {
                $entitlement = $entitlementIds->get($row['code'].'|'.$row['student_level']);

                if (! $entitlement) {
                    continue;
                }

                $rowsToUpsert[] = [
                    'entitlement_id' => $entitlement->id,
                    'item_id' => $row['item_id'],
                    'quantity' => $row['quantity'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                ];
            }

            if ($rowsToUpsert !== []) {
                EntitlementItem::upsert(
                    $rowsToUpsert,
                    ['entitlement_id', 'item_id'],
                    ['quantity', 'updated_at']
                );
            }
        });
    }

    private function resolveStudentLevel(string $rawType): string
    {
        $type = strtolower(trim($rawType));

        return match (true) {
            str_contains($type, 'year 1 sem 1') || str_contains($type, 'y1s1') || str_contains($type, 'freshman') => 'Y1S1',
            str_contains($type, 'year 1 sem 2') || str_contains($type, 'y1s2') => 'Y1S2',
            str_contains($type, 'year 2 sem 1') || str_contains($type, 'year 2 sem 3') || str_contains($type, 'y2s1') || str_contains($type, 'y2s3') => 'Y2S1',
            str_contains($type, 'year 2 sem 2') || str_contains($type, 'year 2 sem 4') || str_contains($type, 'y2s2') || str_contains($type, 'y2s4') => 'Y2S2',
            str_contains($type, 'year 3 sem 1') || str_contains($type, 'y3s1') => 'Y3S1',
            str_contains($type, 'year 3 sem 2') || str_contains($type, 'y3s2') => 'Y3S2',
            str_contains($type, 'year 4 sem 1') || str_contains($type, 'y4s1') => 'Y4S1',
            str_contains($type, 'year 4 sem 2') || str_contains($type, 'y4s2') => 'Y4S2',
            str_contains($type, 'continuing') => 'Y2S1',
            default => 'Y2S1',
        };
    }

    /**
     * Build an in-memory lookup that mirrors the previous SQL matching order:
     * exact name, then shortest name containing the value, then shortest category.
     *
     * @return array{exact: array<string, Item>, contains: array<int, array{name: string, item: Item}>, categories: array<int, array{label: string, item: Item}>}
     */
    private function buildItemIndex(): array
    {
        $items = Item::query()->with('category')->get(['id', 'name', 'category_id']);

        $exact = [];
        $contains = [];
        $categories = [];

        foreach ($items as $item) {
            $name = strtolower(trim((string) $item->name));
            $exact[$name] ??= $item;
            $contains[] = ['name' => $name, 'item' => $item];

            $label = strtolower(trim((string) $item->category?->label));

            if ($label !== '') {
                $categories[] = ['label' => $label, 'item' => $item];
            }
        }

        usort($contains, fn (array $a, array $b): int => strlen($a['name']) <=> strlen($b['name']));
        usort($categories, fn (array $a, array $b): int => strlen((string) $a['item']->name) <=> strlen((string) $b['item']->name));

        return ['exact' => $exact, 'contains' => $contains, 'categories' => $categories];
    }

    /**
     * @param  array{exact: array<string, Item>, contains: array<int, array{name: string, item: Item}>, categories: array<int, array{label: string, item: Item}>}  $index
     */
    protected function findItemByName(string $name, array $index): ?Item
    {
        $normalized = strtolower(trim(str_replace('_', ' ', $name)));

        if ($normalized === '') {
            return null;
        }

        if (isset($index['exact'][$normalized])) {
            return $index['exact'][$normalized];
        }

        foreach ($index['contains'] as $candidate) {
            if (str_contains($candidate['name'], $normalized)) {
                return $candidate['item'];
            }
        }

        foreach ($index['categories'] as $candidate) {
            if (str_contains($candidate['label'], $normalized)) {
                return $candidate['item'];
            }
        }

        return null;
    }

    public function rules(): array
    {
        return [
            'prodi_level' => ['required', 'string'],
            'tipe' => ['required', 'string'],
        ];
    }
}
