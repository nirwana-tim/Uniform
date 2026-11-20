<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
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
        ]);

        $period = filled($validated['period'] ?? null) ? trim($validated['period']) : null;
        $periods = DistributionSchedule::whereNotNull('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->pluck('period', 'period');

        $gpmData = $this->service->calculateGpm($period);
        $gpmByCategory = $this->service->getGpmByCategory($period, $gpmData);

        $totalQty = (int) $gpmData->sum('qty_sold');
        $total_hpp = round((float) $gpmData->sum('total_hpp'), 2);
        $total_selling = round((float) $gpmData->sum('total_selling_price'), 2);
        $total_laba_rugi = round($total_selling - $total_hpp, 2);
        $total_gpm = GpmService::calculateGpmPercentage($total_hpp, $total_selling);

        return view('report.gpm-cost', compact(
            'period',
            'periods',
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
