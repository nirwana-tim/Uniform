<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\Item;
use App\Models\SizeEventSubmission;
use App\Models\Student;
use App\Services\ReportService;
use App\Services\StudentSizeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(Role::SuperAdmin->value) || $user->hasRole(Role::Admin->value)) {
            if ($request->has('get_items') && $request->has('category_id')) {
                $items = Item::where('category_id', $request->input('category_id'))
                    ->orderBy('name')
                    ->get(['id', 'name']);

                return response()->json($items);
            }

            if ($request->wantsJson() || $request->has('ajax')) {
                $processed = app(ReportService::class)->getDashboardAnalyticsData(
                    $request->input('start_date'),
                    $request->input('end_date'),
                    $request->input('category_id') ? (int) $request->input('category_id') : null,
                    $request->input('item_id') ? (int) $request->input('item_id') : null
                );

                return response()->json($processed);
            }

            $data = app(ReportService::class)->getDashboardViewData();

            return view('dashboards.admin', $data);
        }

        if ($user->hasRole(Role::Staff->value)) {
            return $this->staffDashboard();
        }

        return $this->studentDashboard();
    }

    private function staffDashboard(): View
    {
        $activeSchedule = DistributionSchedule::with('generation', 'faculty')
            ->activeNow()
            ->first();

        if (! $activeSchedule) {
            $activeSchedule = DistributionSchedule::with('generation', 'faculty')
                ->where('is_active', true)
                ->where('date', '>=', now()->format('Y-m-d'))
                ->orderBy('date')
                ->first();
        }

        $todayCount = DistributionTransaction::whereDate('created_at', today())
            ->whereIn('status', ['completed', 'partial'])
            ->count();

        $recentTransactions = DistributionTransaction::with('student.user', 'schedule')
            ->withCount('items')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboards.staff', compact('activeSchedule', 'todayCount', 'recentTransactions'));
    }

    private function studentDashboard(): View
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $student->load(['activeSizeProfile', 'studyProgram', 'generation']);

        $sizeService = app(StudentSizeService::class);
        $sizeEvents = $sizeService->getEventsForStudent($student);

        $eventIds = $sizeEvents->pluck('id');
        $submissions = SizeEventSubmission::where('student_id', $student->id)
            ->whereIn('event_id', $eventIds)
            ->get()
            ->keyBy('event_id');

        $activeEvent = $sizeEvents->first();
        $submission = $activeEvent ? $submissions->get($activeEvent->id) : null;
        $canEdit = $activeEvent ? $activeEvent->canEdit($student) : false;

        $distributionSchedules = DistributionSchedule::with(['faculty', 'generation', 'items.item'])
            ->upcomingForStudent($student)
            ->get();

        $activeSchedule = $distributionSchedules->first();

        $profile = $student->activeSizeProfile;

        $totalRequiredSizes = 0;
        $requiresBaju = true;
        $requiresSepatu = true;

        if ($activeEvent) {
            $requiresBaju = ! empty($activeEvent->baju_size_options);
            $requiresSepatu = ! empty($activeEvent->sepatu_size_options);
            if ($requiresBaju) {
                $totalRequiredSizes++;
            }
            if ($requiresSepatu) {
                $totalRequiredSizes++;
            }
        }

        if ($totalRequiredSizes === 0) {
            $totalRequiredSizes = 2;
            $requiresBaju = true;
            $requiresSepatu = true;
        }

        $completedSizeCount = 0;
        if ($requiresBaju && $profile?->baju_size) {
            $completedSizeCount++;
        }
        if ($requiresSepatu && $profile?->sepatu_size) {
            $completedSizeCount++;
        }

        return view('dashboards.student', compact(
            'student', 'sizeEvents', 'activeEvent', 'submissions', 'submission',
            'canEdit', 'distributionSchedules', 'activeSchedule', 'profile', 'completedSizeCount', 'totalRequiredSizes'
        ));
    }
}
