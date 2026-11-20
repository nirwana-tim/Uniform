<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GpmService
{
    /**
     * Calculate Gross Profit Margin ratio (0..1 scale for Excel percentage format).
     *
     * Formula: 1 - (Harga Inventory out (HPP) / Harga Penjualan Bersih)
     */
    public static function calculateGpmRatio(float $totalHpp, float $totalNetSales): float
    {
        if ($totalNetSales <= 0.0) {
            return 0.0;
        }

        return 1.0 - ($totalHpp / $totalNetSales);
    }

    /**
     * Calculate Gross Profit Margin percentage (0..100 scale, rounded to 2 decimals).
     *
     * Formula: 100% - (Harga Inventory out (HPP) / Harga Penjualan Bersih)
     */
    public static function calculateGpmPercentage(float $totalHpp, float $totalNetSales): float
    {
        return round(self::calculateGpmRatio($totalHpp, $totalNetSales) * 100, 2);
    }

    /**
     * @return Collection<int, array{
     *     item_id: int,
     *     item_code: string,
     *     item_name: string,
     *     category_name: string,
     *     qty_sold: int,
     *     hpp: float,
     *     selling_price: float,
     *     total_hpp: float,
     *     total_selling_price: float,
     *     laba_rugi: float,
     *     gpm_ratio: float,
     *     gpm_percentage: float
     * }>
     */
    public function calculateGpm(?string $period = null, ?string $category = null): Collection
    {
        $period = $period !== null ? trim($period) : null;
        $category = $category !== null ? trim($category) : null;

        $query = DB::table('distribution_items')
            ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
            ->join('items', 'distribution_items.item_id', '=', 'items.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->select(
                'distribution_items.item_id',
                'items.code as item_code',
                'items.name as item_name_raw',
                'item_categories.code as category_code',
                'item_categories.label as category_name',
                DB::raw('SUM(distribution_items.quantity) as qty_sold'),
                DB::raw('SUM(distribution_items.quantity * distribution_items.hpp) as total_hpp'),
                DB::raw('SUM(distribution_items.quantity * distribution_items.selling_price_at_distribution) as total_selling_price')
            )
            ->whereIn('distribution_transactions.status', ['completed', 'partial'])
            ->whereNull('distribution_transactions.deleted_at')
            ->where('distribution_items.quantity', '>', 0)
            ->groupBy(
                'distribution_items.item_id',
                'items.code',
                'items.name',
                'item_categories.code',
                'item_categories.label'
            )
            ->orderBy('item_categories.code')
            ->orderBy('items.name');

        if ($period !== null && $period !== '') {
            $query->join('distribution_schedules', 'distribution_transactions.schedule_id', '=', 'distribution_schedules.id')
                ->where('distribution_schedules.period', $period);
        }

        if ($category !== null && $category !== '') {
            $query->where('item_categories.code', $category);
        }

        return $query->get()->map(function (object $row): array {
            $qtySold = (int) $row->qty_sold;
            $totalHpp = round((float) $row->total_hpp, 2);
            $totalSelling = round((float) $row->total_selling_price, 2);
            $labaRugi = round($totalSelling - $totalHpp, 2);
            $avgHpp = $qtySold > 0 ? round($totalHpp / $qtySold, 2) : 0.0;
            $avgSellingPrice = $qtySold > 0 ? round($totalSelling / $qtySold, 2) : 0.0;
            $gpmRatio = self::calculateGpmRatio($totalHpp, $totalSelling);

            return [
                'item_id' => (int) $row->item_id,
                'item_code' => (string) ($row->item_code ?? ''),
                'item_name' => (string) ($row->item_name_raw ?? '-'),
                'category_name' => (string) ($row->category_name ?? '-'),
                'qty_sold' => $qtySold,
                'hpp' => $avgHpp,
                'selling_price' => $avgSellingPrice,
                'total_hpp' => $totalHpp,
                'total_selling_price' => $totalSelling,
                'laba_rugi' => $labaRugi,
                'gpm_ratio' => $gpmRatio,
                'gpm_percentage' => round($gpmRatio * 100, 2),
            ];
        });
    }

    /**
     * @param  Collection<int, array<string, mixed>>|null  $gpmData
     * @return Collection<int, array{
     *     category: string,
     *     total_qty: int,
     *     total_hpp: float,
     *     total_selling_price: float,
     *     laba_rugi: float,
     *     gpm_ratio: float,
     *     gpm_percentage: float
     * }>
     */
    public function getGpmByCategory(?string $period = null, ?Collection $gpmData = null): Collection
    {
        $gpmData ??= $this->calculateGpm($period);

        return $gpmData->groupBy('category_name')->map(function (Collection $items, string $category): array {
            $totalQty = (int) $items->sum('qty_sold');
            $totalHpp = round((float) $items->sum('total_hpp'), 2);
            $totalSelling = round((float) $items->sum('total_selling_price'), 2);
            $labaRugi = round($totalSelling - $totalHpp, 2);
            $gpmRatio = self::calculateGpmRatio($totalHpp, $totalSelling);

            return [
                'category' => $category,
                'total_qty' => $totalQty,
                'total_hpp' => $totalHpp,
                'total_selling_price' => $totalSelling,
                'laba_rugi' => $labaRugi,
                'gpm_ratio' => $gpmRatio,
                'gpm_percentage' => round($gpmRatio * 100, 2),
            ];
        })->values();
    }

    /**
     * @return Collection<int, array{
     *     period: string,
     *     qty_sold: int,
     *     total_hpp: float,
     *     total_selling_price: float,
     *     laba_rugi: float,
     *     gpm_ratio: float,
     *     gpm_percentage: float
     * }>
     */
    public function getGpmByPeriod(): Collection
    {
        $results = DB::table('distribution_items')
            ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
            ->join('distribution_schedules', 'distribution_transactions.schedule_id', '=', 'distribution_schedules.id')
            ->join('items', 'distribution_items.item_id', '=', 'items.id')
            ->select(
                'distribution_schedules.period',
                DB::raw('SUM(distribution_items.quantity) as qty_sold'),
                DB::raw('SUM(distribution_items.quantity * distribution_items.hpp) as total_hpp'),
                DB::raw('SUM(distribution_items.quantity * distribution_items.selling_price_at_distribution) as total_selling_price')
            )
            ->whereIn('distribution_transactions.status', ['completed', 'partial'])
            ->whereNull('distribution_transactions.deleted_at')
            ->where('distribution_items.quantity', '>', 0)
            ->whereNotNull('distribution_schedules.period')
            ->groupBy('distribution_schedules.period')
            ->orderBy('distribution_schedules.period')
            ->get();

        return $results->map(function (object $row): array {
            $totalHpp = round((float) $row->total_hpp, 2);
            $totalSelling = round((float) $row->total_selling_price, 2);
            $labaRugi = round($totalSelling - $totalHpp, 2);
            $gpmRatio = self::calculateGpmRatio($totalHpp, $totalSelling);

            return [
                'period' => (string) $row->period,
                'qty_sold' => (int) $row->qty_sold,
                'total_hpp' => $totalHpp,
                'total_selling_price' => $totalSelling,
                'laba_rugi' => $labaRugi,
                'gpm_ratio' => $gpmRatio,
                'gpm_percentage' => round($gpmRatio * 100, 2),
            ];
        });
    }
}
