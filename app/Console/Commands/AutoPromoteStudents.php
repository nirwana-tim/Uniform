<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentGeneration;
use App\Services\Master\StudentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AutoPromoteStudents extends Command
{
    protected $signature = 'students:auto-promote
        {--dry-run : Preview changes without saving}
        {--due : Only run when the academic term rollover window is open}
        {--semester= : Target semester e.g. Y1S1, Y2S3}
        {--level-id= : Target generation_id for promoted students}';

    protected $description = 'Promote all active students to the next semester';

    private const PROMOTION_MONTHS = [2, 8];

    private const PROMOTION_WINDOW_DAYS = 7;

    private const SEMESTERS = ['Y1S1', 'Y1S2', 'Y2S1', 'Y2S2', 'Y3S1', 'Y3S2', 'Y4S1', 'Y4S2'];

    private array $semesterMap = [
        '' => 'Y1S1',
        'Y1S1' => 'Y1S2',
        'Y1S2' => 'Y2S1',
        'Y2S1' => 'Y2S2',
        'Y2S2' => 'Y3S1',
        'Y3S1' => 'Y3S2',
        'Y3S2' => 'Y4S1',
        'Y4S1' => 'Y4S2',
        'Y4S2' => 'graduated',
    ];

    /**
     * @var array<string, StudentGeneration|null>
     */
    private array $generationCache = [];

    public function handle(): int
    {
        $targetSemester = $this->option('semester');
        $levelIdOption = $this->option('level-id');
        $targetLevelId = null;

        if ($levelIdOption !== null && $levelIdOption !== '') {
            if (! ctype_digit((string) $levelIdOption)) {
                $this->error('Opsi --level-id harus berupa angka (generation_id).');

                return self::FAILURE;
            }

            $targetLevelId = (int) $levelIdOption;
        }

        $dryRun = $this->option('dry-run');

        if ($this->option('due') && ! $this->isPromotionDue()) {
            $this->info('Bukan periode kenaikan semester. Tidak ada mahasiswa yang dipromosikan.');

            return self::SUCCESS;
        }

        $lockKey = 'students:auto-promote:'.now()->format('Y-m');

        if (! $dryRun && $this->option('due') && ! Cache::add($lockKey, now()->toDateTimeString(), now()->addDays(45))) {
            $this->info('Promosi semester periode ini sudah dijalankan sebelumnya.');

            return self::SUCCESS;
        }

        $studentIds = [];
        $previewRows = [];

        Student::with('generation')
            ->where(function ($q) use ($targetSemester) {
                if ($targetSemester) {
                    $q->where('current_semester', $targetSemester);
                } else {
                    $q->whereIn('current_semester', self::SEMESTERS)
                        ->orWhereNull('current_semester')
                        ->orWhere('current_semester', '');
                }
            })
            ->whereIn('status', ['active', 'leave'])
            ->chunk(100, function ($chunk) use ($targetLevelId, $dryRun, &$studentIds, &$previewRows) {
                foreach ($chunk as $student) {
                    $next = $this->resolveNextSemester($student);
                    $genId = $this->resolveGenerationId($student, $targetLevelId, ! $dryRun);

                    if ($dryRun) {
                        $previewRows[] = sprintf(
                            '[DRY-RUN] %s (%s): level %s → %s, sem %s → %s, gen %s',
                            $student->nim,
                            $student->name,
                            $student->student_level,
                            $next,
                            $student->current_semester ?? '-',
                            $this->semesterMap[$student->current_semester ?? ''] ?? '?',
                            $genId ?? 'auto',
                        );
                    } else {
                        $studentIds[] = $student->id;
                    }
                }
            });

        if ($dryRun) {
            foreach ($previewRows as $row) {
                $this->line($row);
            }
            $this->info(sprintf("\n%d students would be promoted.", count($previewRows)));

            return self::SUCCESS;
        }

        if (empty($studentIds)) {
            $this->warn('No eligible students found for promotion.');

            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($studentIds, $targetLevelId) {
                foreach (array_chunk($studentIds, 100) as $chunkIds) {
                    $count = app(StudentService::class)->promoteStudents($chunkIds, $targetLevelId);
                    $this->line(sprintf('  ✓ %d students promoted.', $count));
                }
            });
        } catch (\Throwable $e) {
            if ($this->option('due')) {
                Cache::forget($lockKey);
            }

            throw $e;
        }

        $this->info(sprintf("\n%d students promoted successfully.", count($studentIds)));

        return self::SUCCESS;
    }

    private function resolveNextSemester(Student $student): string
    {
        $sem = $student->current_semester ?? '';

        return $this->semesterMap[$sem] ?? 'Y1S1';
    }

    private function isPromotionDue(): bool
    {
        $today = now();

        return in_array($today->month, self::PROMOTION_MONTHS, true)
            && $today->day <= self::PROMOTION_WINDOW_DAYS;
    }

    private function resolveGenerationId(Student $student, ?int $overrideId, bool $createIfMissing = false): ?int
    {
        if ($overrideId) {
            return $overrideId;
        }

        $code = $student->generation?->code;

        if (! $code || ! preg_match('/^\d{4}$/', (string) $code)) {
            $this->warn("Kode generasi '{$code}' tidak valid untuk NIM {$student->nim}; promosi generasi dilewati.");

            return null;
        }

        $startYear = (int) substr($code, 0, 2);
        $endYear = (int) substr($code, 2, 2);

        if ($endYear !== $startYear + 1 || $startYear + 1 > 99) {
            $this->warn("Kode generasi '{$code}' tidak berurutan untuk NIM {$student->nim}; promosi generasi dilewati.");

            return null;
        }

        $nextStartYear = $startYear + 1;
        $nextCode = sprintf('%02d%02d', $nextStartYear, $nextStartYear + 1);

        if (array_key_exists($nextCode, $this->generationCache)) {
            $generation = $this->generationCache[$nextCode];
        } else {
            $generation = StudentGeneration::where('code', $nextCode)->first();
            $this->generationCache[$nextCode] = $generation;
        }

        if (! $generation && $createIfMissing) {
            $generation = StudentGeneration::create([
                'code' => $nextCode,
                'name' => '20'.$nextStartYear.'/'.'20'.($nextStartYear + 1),
            ]);
            $this->generationCache[$nextCode] = $generation;
            $this->line("  + Generasi {$nextCode} dibuat otomatis.");
        }

        return $generation?->id;
    }
}
