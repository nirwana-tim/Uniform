<?php

namespace App\Imports;

use App\Models\Faculty;
use App\Models\Item;
use App\Models\Student;
use App\Models\StudentGeneration;
use App\Models\StudentSizeItem;
use App\Models\StudentSizeProfile;
use App\Models\StudyProgram;
use App\Models\User;
use App\Services\Master\GenerationResolverService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;

class StudentImport implements ToCollection, WithMultipleSheets
{
    private int $totalRows = 0;

    private int $importedCount = 0;

    /** @var Collection<int, StudyProgram>|null */
    private ?Collection $studyPrograms = null;

    /** @var Collection<string, StudentGeneration> */
    private Collection $generations;

    /** @var Collection<string, int> */
    private Collection $studentIds;

    public function collection(Collection $rows): void
    {
        $this->processRows($rows);
    }

    public function processRows(Collection $rows): void
    {
        $records = $this->recordsFromRows($rows);
        $this->totalRows = count($records);
        $this->studyPrograms = StudyProgram::query()->with('faculty')->get();
        $this->generations = StudentGeneration::query()->get()->keyBy('code');

        $failures = $this->validateRecords($records);

        if ($failures !== []) {
            throw new ValidationException(
                IlluminateValidationException::withMessages([]),
                $failures
            );
        }

        $items = Item::query()->get(['id', 'name', 'code']);

        $shirtItem = $items->first(fn ($i) => str_contains(strtolower($i->name), 'uniform')
            || str_contains(strtolower($i->name), 'seragam')
            || str_contains(strtolower($i->code), 'unf'));

        $shoeItem = $items->first(fn ($i) => str_contains(strtolower($i->name), 'sepatu')
            || str_contains(strtolower($i->name), 'shoe')
            || str_contains(strtolower($i->code), 'sho'));

        DB::transaction(function () use ($records, $shirtItem, $shoeItem) {
            $this->persistStudents($records);
            $this->persistSizes($records, $shirtItem, $shoeItem);
            $this->importedCount = count($records);
        });
    }

    /**
     * Upsert every student row in one statement, then read the ids back once.
     *
     * @param  array<int, array<string, mixed>>  $records
     */
    private function persistStudents(array $records): void
    {
        if ($records === []) {
            $this->studentIds = collect();

            return;
        }

        $now = now();
        $rows = [];

        foreach ($records as $record) {
            $rows[] = [
                'nim' => $record['nim'],
                'name' => $record['name'],
                'gender' => $record['gender'],
                'email_kampus' => $record['email_kampus'],
                'email_pribadi' => $record['email_pribadi'],
                'study_program_id' => $record['study_program']->id,
                'generation_id' => $this->resolveProgramLevel($record['nim'])->id,
                'student_level' => $record['student_level'],
                'entitlement_code' => ($record['student_level'] ?? 'Y1S1')
                    .($record['study_program']->faculty->code ?? 'FHS')
                    .$record['study_program']->code,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Student::upsert(
            $rows,
            ['nim'],
            [
                'name',
                'gender',
                'email_kampus',
                'email_pribadi',
                'study_program_id',
                'generation_id',
                'student_level',
                'entitlement_code',
                'updated_at',
            ]
        );

        $this->studentIds = Student::query()
            ->whereIn('nim', array_column($rows, 'nim'))
            ->pluck('id', 'nim');
    }

    /**
     * Create the missing size profiles in one insert and upsert every size row
     * in one statement, instead of four queries per student.
     *
     * @param  array<int, array<string, mixed>>  $records
     */
    private function persistSizes(array $records, ?Item $shirtItem, ?Item $shoeItem): void
    {
        $withSizes = array_values(array_filter(
            $records,
            fn (array $record): bool => ! empty($record['shirt_size']) || ! empty($record['shoe_size'])
        ));

        if ($withSizes === []) {
            return;
        }

        $now = now();
        $studentIds = $this->studentIds->all();
        $existingProfiles = StudentSizeProfile::query()
            ->whereIn('student_id', array_values($studentIds))
            ->pluck('student_id')
            ->flip();

        $newProfiles = [];
        foreach ($withSizes as $record) {
            $studentId = $studentIds[$record['nim']] ?? null;

            if (! $studentId || $existingProfiles->has($studentId)) {
                continue;
            }

            $existingProfiles[$studentId] = true;
            $newProfiles[] = [
                'student_id' => $studentId,
                'is_filled' => true,
                'filled_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($newProfiles !== []) {
            StudentSizeProfile::insert($newProfiles);
        }

        $profiles = StudentSizeProfile::query()
            ->whereIn('student_id', array_values($studentIds))
            ->pluck('id', 'student_id');

        $sizeRows = [];
        $seen = [];

        foreach ($withSizes as $record) {
            $studentId = $studentIds[$record['nim']] ?? null;
            $profileId = $studentId ? $profiles[$studentId] ?? null : null;

            if (! $profileId) {
                continue;
            }

            $pairs = [
                ['item' => $shirtItem, 'size' => $record['shirt_size'], 'label' => 'seragam'],
                ['item' => $shoeItem, 'size' => $record['shoe_size'], 'label' => 'sepatu'],
            ];

            foreach ($pairs as $pair) {
                if (empty($pair['size'])) {
                    continue;
                }

                if (! $pair['item']) {
                    Log::warning("Item {$pair['label']} tidak ditemukan; ukuran dilewati.", [
                        'nim' => $record['nim'],
                        'size' => $pair['size'],
                    ]);

                    continue;
                }

                $key = $profileId.'-'.$pair['item']->id;

                if (isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;
                $sizeRows[] = [
                    'size_profile_id' => $profileId,
                    'item_id' => $pair['item']->id,
                    'size' => $pair['size'],
                    'change_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($sizeRows !== []) {
            StudentSizeItem::upsert(
                $sizeRows,
                ['size_profile_id', 'item_id'],
                ['size', 'change_count', 'updated_at']
            );
        }
    }

    public function sheets(): array
    {
        return [
            'Data' => new class($this) implements ToCollection
            {
                public function __construct(private StudentImport $parent) {}

                public function collection(Collection $rows): void
                {
                    $this->parent->processRows($rows);
                }
            },
        ];
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
            $values = array_values($row instanceof Collection ? $row->toArray() : (array) $row);

            if ($this->shouldSkipRow($values)) {
                continue;
            }

            $nim = $this->clean($values[0] ?? null);
            if (! $nim) {
                continue;
            }

            $emailKampus = $this->clean($values[6] ?? null);

            $records[] = [
                'row' => $index + 1,
                'nim' => $nim,
                'name' => $this->clean($values[1] ?? null),
                'program' => $this->clean($values[2] ?? null),
                'gender' => $this->normalizeGender($this->clean($values[3] ?? null)),
                'shirt_size' => $this->clean($values[4] ?? null),
                'shoe_size' => $this->clean($values[5] ?? null),
                'email_kampus' => $emailKampus,
                'email_pribadi' => $this->clean($values[7] ?? null),
                'student_type_raw' => $this->clean($values[8] ?? null),
            ];
        }

        return $records;
    }

    private function validateRecords(array &$records): array
    {
        $failures = [];
        $seenNims = [];
        $seenEmails = [];
        $emailOwners = $this->buildEmailOwners($records);

        foreach ($records as &$record) {
            $validator = Validator::make($record, [
                'nim' => ['required', 'string', 'max:20'],
                'name' => ['required', 'string', 'max:255'],
                'program' => ['required', 'string'],
                'email_kampus' => ['nullable', 'email', 'max:255'],
                'email_pribadi' => ['nullable', 'email', 'max:255'],
            ]);

            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $failures[] = new Failure($record['row'], $attribute, $messages, $record);
            }

            if (isset($seenNims[$record['nim']])) {
                $failures[] = new Failure($record['row'], 'nim', ['NIM duplikat pada file ini.'], $record);
            }
            $seenNims[$record['nim']] = true;

            foreach (['email_kampus', 'email_pribadi'] as $field) {
                $email = $record[$field] ?? null;

                if (! $email) {
                    continue;
                }

                $key = Str::lower($email);

                if (isset($seenEmails[$key])) {
                    $failures[] = new Failure($record['row'], $field, ['Email duplikat pada file ini.'], $record);

                    continue;
                }

                $seenEmails[$key] = true;

                $owner = $emailOwners[$key] ?? null;

                if ($owner !== null && $owner !== $record['nim']) {
                    $failures[] = new Failure($record['row'], $field, ['Email sudah digunakan oleh mahasiswa atau akun lain.'], $record);
                }
            }

            $record['study_program'] = $this->resolveStudyProgram($record['program']);
            $record['program_level'] = $this->resolveProgramLevel($record['nim']);

            $rawType = $record['student_type_raw'] ?? '';
            $type = strtolower($rawType);

            $record['student_level'] = match (true) {
                str_contains($type, 'graduated') || str_contains($type, 'lulus') || str_contains($type, 'alumni') => 'graduated',
                str_contains($type, 'year 1 sem 1') || str_contains($type, 'y1s1') || str_contains($type, 'freshman') => 'Y1S1',
                str_contains($type, 'year 1 sem 2') || str_contains($type, 'y1s2') => 'Y1S2',
                str_contains($type, 'year 2 sem 1') || str_contains($type, 'year 2 sem 3') || str_contains($type, 'y2s1') || str_contains($type, 'y2s3') => 'Y2S1',
                str_contains($type, 'year 2 sem 2') || str_contains($type, 'year 2 sem 4') || str_contains($type, 'y2s2') || str_contains($type, 'y2s4') => 'Y2S2',
                str_contains($type, 'year 3 sem 1') || str_contains($type, 'y3s1') => 'Y3S1',
                str_contains($type, 'year 3 sem 2') || str_contains($type, 'y3s2') => 'Y3S2',
                str_contains($type, 'year 4 sem 1') || str_contains($type, 'y4s1') => 'Y4S1',
                str_contains($type, 'year 4 sem 2') || str_contains($type, 'y4s2') => 'Y4S2',
                str_contains($type, 'continuing') => 'Y2S1',
                default => 'Y1S1',
            };

            if ($rawType && $record['student_level'] === 'Y1S1' && ! str_contains($type, 'year 1 sem 1')
                && ! str_contains($type, 'y1s1') && ! str_contains($type, 'freshman')) {
                $failures[] = new Failure($record['row'], 'student_type_raw', ["Tipe mahasiswa '{$rawType}' tidak dikenali, default ke {$record['student_level']}."], $record);
            }
        }

        return $failures;
    }

    private function shouldSkipRow(array $values): bool
    {
        $firstCell = $this->clean($values[0] ?? null);

        if ($firstCell === null) {
            return collect($values)->filter(fn ($value): bool => $this->clean($value) !== null)->isEmpty();
        }

        return Str::startsWith(Str::upper($firstCell), [
            'TEMPLATE IMPORT',
            'ISI DATA',
            'URUTAN KOLOM',
            'NIM',
            'CONTOH FORMAT',
            'CONTOH',
        ]);
    }

    /**
     * Map lower-cased email => owning NIM ("__account__" for non-student accounts).
     *
     * @param  array<int, array<string, mixed>>  $records
     * @return array<string, string>
     */
    private function buildEmailOwners(array $records): array
    {
        $emails = collect($records)
            ->flatMap(fn (array $record): array => [$record['email_kampus'] ?? null, $record['email_pribadi'] ?? null])
            ->filter()
            ->map(fn (string $email): string => Str::lower($email))
            ->unique()
            ->values();

        if ($emails->isEmpty()) {
            return [];
        }

        $owners = [];

        $students = Student::query()
            ->where(function ($query) use ($emails) {
                $query->whereIn(DB::raw('LOWER(email_kampus)'), $emails)
                    ->orWhereIn(DB::raw('LOWER(email_pribadi)'), $emails);
            })
            ->get(['nim', 'user_id', 'email_kampus', 'email_pribadi']);

        foreach ($students as $student) {
            foreach ([$student->email_kampus, $student->email_pribadi] as $email) {
                if ($email) {
                    $owners[Str::lower($email)] ??= $student->nim;
                }
            }
        }

        $users = User::query()
            ->whereIn(DB::raw('LOWER(email)'), $emails)
            ->get(['id', 'email']);

        if ($users->isNotEmpty()) {
            $nimByUser = Student::query()
                ->whereIn('user_id', $users->pluck('id'))
                ->pluck('nim', 'user_id');

            foreach ($users as $user) {
                $owners[Str::lower($user->email)] ??= $nimByUser[$user->id] ?? '__account__';
            }
        }

        return $owners;
    }

    private function resolveStudyProgram(?string $value): StudyProgram
    {
        if (! $value) {
            $value = 'Umum';
        }

        $normalized = $this->normalizeProgramName($value);

        $this->studyPrograms ??= StudyProgram::query()->with('faculty')->get();

        $found = $this->studyPrograms->first(function (StudyProgram $program) use ($value, $normalized): bool {
            $pNorm = $this->normalizeProgramName($program->name);

            return Str::lower($program->code) === Str::lower($value)
                || $pNorm === $normalized
                || str_contains($pNorm, $normalized)
                || str_contains($normalized, $pNorm);
        });

        if ($found) {
            return $found;
        }

        $faculty = Faculty::first() ?? Faculty::create(['code' => 'FHS', 'name' => 'Fakultas Kesehatan & Sains']);
        $code = Str::upper(Str::slug($value, ''));
        $code = strlen($code) > 10 ? substr($code, 0, 10) : ($code ?: 'PRODI');

        $program = StudyProgram::firstOrCreate(
            ['name' => trim($value)],
            [
                'code' => $code,
                'faculty_id' => $faculty->id,
            ]
        );

        $this->studyPrograms->push($program->loadMissing('faculty'));

        return $program;
    }

    private function resolveProgramLevel(?string $nim): StudentGeneration
    {
        $code = $nim ? app(GenerationResolverService::class)->parseCodeFromNim($nim) : null;

        if ($code !== null) {
            if (! isset($this->generations[$code])) {
                $yearPrefix = '20'.substr($code, 0, 2);
                $yearSuffix = '20'.substr($code, 2, 2);

                $this->generations[$code] = StudentGeneration::firstOrCreate(
                    ['code' => $code],
                    ['name' => $yearPrefix.'/'.$yearSuffix]
                );
            }

            return $this->generations[$code];
        }

        if ($this->generations->isEmpty()) {
            $generation = StudentGeneration::first()
                ?? StudentGeneration::create(['code' => '2526', 'name' => '2025/2026']);

            $this->generations[$generation->code] = $generation;
        }

        $generation = $this->generations->first();

        if (! $generation instanceof StudentGeneration) {
            throw new \RuntimeException('Gagal menyiapkan data generasi untuk import mahasiswa.');
        }

        return $generation;
    }

    private function normalizeGender(?string $gender): ?string
    {
        if (! $gender) {
            return null;
        }
        $g = Str::upper(trim($gender));
        if ($g === 'P' || str_contains($g, 'PEREMPUAN') || str_contains($g, 'FEMALE')) {
            return 'P';
        }
        if ($g === 'L' || str_contains($g, 'LAKI') || str_contains($g, 'MALE')) {
            return 'L';
        }

        return null;
    }

    private function normalizeProgramName(string $value): string
    {
        return Str::of($value)
            ->upper()
            ->replaceMatches('/\s+\d+$/', '')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();
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
}
