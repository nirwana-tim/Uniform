<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\SizeChangeEvent;
use App\Models\SizeEventSubmission;
use App\Models\Student;
use App\Services\QrCodeService;
use App\Services\StudentSizeService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeController extends Controller
{
    public function __construct(
        private readonly StudentSizeService $sizeService,
        private readonly QrCodeService $qrCodeService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        $student->load(['activeSizeProfile', 'studyProgram', 'generation']);

        $events = $this->sizeService->getEventsForStudent($student);

        $eventIds = $events->pluck('id');
        $submissions = SizeEventSubmission::where('student_id', $student->id)
            ->whereIn('event_id', $eventIds)
            ->get()
            ->keyBy('event_id');

        $activeEvent = $events->first();
        $submission = $activeEvent ? $submissions->get($activeEvent->id) : null;
        $canEdit = $activeEvent ? $activeEvent->canEdit($student) : false;
        $remainingChanges = $activeEvent ? max(0, $activeEvent->max_changes - ($submission?->submission_count ?? 0)) : 0;

        $profile = $student->activeSizeProfile;
        $distributionSchedules = DistributionSchedule::upcomingForStudent($student)->get();

        return view('student.sizes-index', compact(
            'student', 'events', 'activeEvent', 'submissions', 'submission',
            'canEdit', 'remainingChanges', 'distributionSchedules', 'profile'
        ));
    }

    public function input(SizeChangeEvent $event): View
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        if (! $event->isApplicableToStudent($student)) {
            abort(403, 'Event ini tidak berlaku untuk kamu.');
        }

        $sizeOptions = $this->sizeService->getSizeOptions($event);

        $profile = $student->activeSizeProfile;
        $currentSizes = [
            'baju' => $profile?->baju_size ?? null,
            'sepatu' => $profile?->sepatu_size ?? null,
        ];

        $submission = SizeEventSubmission::where('student_id', $student->id)
            ->where('event_id', $event->id)->first();
        $submissionCount = $submission?->submission_count ?? 0;
        $canEdit = $event->canEdit($student);
        $remainingChanges = max(0, $event->max_changes - $submissionCount);

        return view('student.size-input', compact(
            'student', 'event', 'sizeOptions', 'currentSizes',
            'canEdit', 'remainingChanges'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'sizes' => 'required|array',
            'sizes.baju' => 'nullable|string|max:10',
            'sizes.sepatu' => 'nullable|string|max:10',
            'event_id' => 'required|integer|exists:size_change_events,id',
        ]);

        try {
            $this->sizeService->saveSizes($student, $validated['sizes'], $validated['event_id']);
        } catch (\RuntimeException $e) {
            return redirect()->route('student.sizes.input', $validated['event_id'])
                ->with('error', $e->getMessage());
        }

        return redirect()->route('student.sizes.index')
            ->with('success', 'Ukuran seragam berhasil diperbarui.');
    }

    public function scheduleDetail(DistributionSchedule $schedule): View
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        $schedule->load(['items.item', 'faculty', 'generation', 'studyProgram']);

        return view('student.schedules.show', compact('student', 'schedule'));
    }

    public function qr(): View
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        $student->load(['activeSizeProfile', 'studyProgram', 'generation', 'eligibilityRecords']);

        $isEligible = $student->eligibilityRecords ? (bool) $student->eligibilityRecords->is_eligible : true;
        $qrDataUrl = $this->qrCodeService->getQrPngDataUrl($student);

        $activeSchedules = DistributionSchedule::with(['faculty', 'generation', 'items.item'])
            ->where('is_active', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->forStudent($student)
            ->orderBy('date')
            ->take(5)
            ->get();

        $events = $this->sizeService->getEventsForStudent($student);
        $activeEvent = $events->first();
        $profile = $student->activeSizeProfile;

        $completedSizeCount = 0;
        if ($profile?->baju_size) {
            $completedSizeCount++;
        }
        if ($profile?->sepatu_size) {
            $completedSizeCount++;
        }

        return view('student.qr-show', compact(
            'student', 'activeSchedules', 'qrDataUrl', 'isEligible',
            'activeEvent', 'profile', 'completedSizeCount'
        ));
    }

    public function items(): View
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();
        $student->load(['activeSizeProfile.sizeItems.item', 'studyProgram', 'generation']);

        $entitlementItems = $this->sizeService->getEntitlementItems($student);
        if ($entitlementItems instanceof Collection) {
            $entitlementItems->loadMissing('category');
        } else {
            $entitlementItems->each(fn ($item) => method_exists($item, 'loadMissing') ? $item->loadMissing('category') : null);
        }

        $receivedTransactions = DistributionTransaction::with(['items.item', 'schedule'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['completed', 'partial'])
            ->latest()
            ->get();

        $selectedSizes = [];
        if ($student->activeSizeProfile) {
            foreach ($student->activeSizeProfile->sizeItems as $si) {
                $selectedSizes[$si->item_id] = $si->size;
            }

            $bajuSize = $student->activeSizeProfile->baju_size;
            $sepatuSize = $student->activeSizeProfile->sepatu_size;
            if ($bajuSize || $sepatuSize) {
                foreach ($entitlementItems as $item) {
                    if (isset($selectedSizes[$item->id])) {
                        continue;
                    }
                    $catCode = $item->category?->code;
                    if ($catCode === 'UNF' && $bajuSize) {
                        $selectedSizes[$item->id] = $bajuSize;
                    } elseif ($catCode === 'SHO' && $sepatuSize) {
                        $selectedSizes[$item->id] = $sepatuSize;
                    }
                }
            }
        }

        $receivedItemIds = $receivedTransactions->flatMap->items->pluck('item_id')->unique()->toArray();
        $pendingItems = $entitlementItems->filter(fn ($item) => ! in_array($item->id, $receivedItemIds));

        return view('student.items', compact(
            'student', 'entitlementItems', 'receivedTransactions',
            'selectedSizes', 'receivedItemIds', 'pendingItems'
        ));
    }
}
