<?php

namespace App\Services\Master;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemDepartment;
use App\Models\ItemSize;
use App\Models\ItemType;
use App\Models\ItemVariant;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemService
{
    public function store(array $data): Item
    {
        $category = ItemCategory::findOrFail($data['category_id']);
        $type = ! empty($data['type_id']) ? ItemType::findOrFail($data['type_id']) : null;
        $department = ! empty($data['department_id']) ? ItemDepartment::findOrFail($data['department_id']) : null;
        $sizes = ItemSize::whereIn('id', $data['size_ids'])->get();

        $genderLabels = ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'];

        $code = $category->code.'-'.$data['gender'].'-'.($type?->code ?? 'XX').'-'.($department?->code ?? '00');
        $name = $category->label.' '.($genderLabels[$data['gender']] ?? '').' '.($type?->label ?? '').' '.($department?->label ?? '');

        if (Item::where('code', $code)->exists()) {
            throw ValidationException::withMessages([
                'category_id' => "Item dengan kode {$code} sudah ada.",
            ]);
        }

        try {
            $item = Item::create([
                'base_code' => $code,
                'code' => $code,
                'name' => trim($name),
                'gender' => $data['gender'],
                'category_id' => $data['category_id'],
                'type_id' => $data['type_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'unit' => $data['unit'] ?? 'pcs',
                'selling_price' => $data['selling_price'] ?? 0,
                'hpp' => $data['hpp'] ?? 0,
            ]);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                throw ValidationException::withMessages([
                    'category_id' => "Item dengan kode {$code} sudah ada. Silakan coba lagi.",
                ]);
            }
            throw $e;
        }

        $skusToCreate = $sizes->map(fn ($size) => $code.'-'.$size->code)->toArray();
        ItemVariant::whereNull('item_id')->whereIn('sku', $skusToCreate)->delete();

        $this->insertMissingVariants($item, $sizes, $code, false);

        return $item;
    }

    public function update(Item $item, array $data): Item
    {
        $old = $item->toArray();

        $category = ItemCategory::findOrFail($data['category_id']);
        $type = ! empty($data['type_id']) ? ItemType::findOrFail($data['type_id']) : null;
        $department = ! empty($data['department_id']) ? ItemDepartment::findOrFail($data['department_id']) : null;
        $sizes = ItemSize::whereIn('id', $data['size_ids'])->get();

        $genderLabels = ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'];

        $newCode = $category->code.'-'.$data['gender'].'-'.($type?->code ?? 'XX').'-'.($department?->code ?? '00');
        $newName = trim($category->label.' '.($genderLabels[$data['gender']] ?? '').' '.($type?->label ?? '').' '.($department?->label ?? ''));

        if ($newCode !== $item->code && Item::where('code', $newCode)->exists()) {
            throw ValidationException::withMessages([
                'category_id' => "Item dengan kode {$newCode} sudah ada.",
            ]);
        }

        $updateData = $data;
        $updateData['code'] = $newCode;
        $updateData['base_code'] = $newCode;
        $updateData['name'] = $newName;
        unset($updateData['size_ids']);

        $item->update($updateData);

        if ($newCode !== $old['code']) {
            $quotedCode = DB::connection()->getPdo()->quote($newCode);

            DB::table('item_variants')
                ->where('item_id', $item->id)
                ->update([
                    'sku' => DB::raw("CONCAT({$quotedCode}, '-', size)"),
                    'updated_at' => now(),
                ]);
        }

        $skusToCreate = $sizes->map(fn ($size) => $newCode.'-'.$size->code)->toArray();
        ItemVariant::whereNull('item_id')->whereIn('sku', $skusToCreate)->delete();

        $this->insertMissingVariants($item, $sizes, $newCode);

        return $item;
    }

    private function insertMissingVariants(Item $item, Collection $sizes, string $code, bool $checkExisting = true): void
    {
        if ($sizes->isEmpty()) {
            return;
        }

        $existingSizeIds = $checkExisting
            ? $item->variants()
                ->whereIn('size_id', $sizes->pluck('id'))
                ->pluck('size_id')
                ->flip()
            : collect();

        $now = now();
        $rows = [];

        foreach ($sizes as $size) {
            if ($existingSizeIds->has($size->id)) {
                continue;
            }

            $rows[] = [
                'item_id' => $item->id,
                'size_id' => $size->id,
                'size' => $size->code,
                'size_label' => $size->label,
                'sku' => $code.'-'.$size->code,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            ItemVariant::insert($rows);
        }
    }

    public function destroy(Item $item): void
    {
        $item->delete();
    }
}
