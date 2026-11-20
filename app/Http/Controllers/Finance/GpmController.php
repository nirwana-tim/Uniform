<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Services\GpmService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GpmController extends Controller
{
    public function __construct(
        private GpmService $service
    ) {}

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'period' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:50'],
            'item_id' => ['nullable', 'integer', 'exists:items,id'],
            'item_code' => ['nullable', 'string', 'exists:items,code'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $period = filled($validated['period'] ?? null) ? trim($validated['period']) : null;
        $category = filled($validated['category'] ?? null) ? trim($validated['category']) : null;
        $itemId = ! empty($validated['item_id']) ? (int) $validated['item_id'] : null;
        if (! $itemId && filled($validated['item_code'] ?? null)) {
            $itemId = Item::where('code', trim($validated['item_code']))->value('id');
        }
        $startDate = filled($validated['start_date'] ?? null) ? trim($validated['start_date']) : null;
        $endDate = filled($validated['end_date'] ?? null) ? trim($validated['end_date']) : null;

        $periods = DistributionSchedule::whereNotNull('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->pluck('period', 'period');

        $items = Item::orderBy('name')->get(['id', 'code', 'name']);
        $categories = ItemCategory::orderBy('code')->get(['id', 'code', 'label']);

        $gpmData = $this->service->calculateGpm($period, $category, $startDate, $endDate, $itemId);
        $gpmByCategory = $this->service->getGpmByCategory($period, $gpmData, $category, $startDate, $endDate, $itemId);

        $totalQty = (int) $gpmData->sum('qty_sold');
        $total_hpp = round((float) $gpmData->sum('total_hpp'), 2);
        $total_selling = round((float) $gpmData->sum('total_selling_price'), 2);
        $total_laba_rugi = round($total_selling - $total_hpp, 2);
        $total_gpm = GpmService::calculateGpmPercentage($total_hpp, $total_selling);

        return view('report.gpm-cost', compact(
            'period',
            'periods',
            'category',
            'categories',
            'itemId',
            'items',
            'startDate',
            'endDate',
            'gpmData',
            'gpmByCategory',
            'totalQty',
            'total_hpp',
            'total_selling',
            'total_laba_rugi',
            'total_gpm'
        ));
    }
}
