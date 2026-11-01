<?php

namespace App\Console\Commands;

use App\Models\DistributionTransaction;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateStudentSummaries extends Command
{
    protected $signature = 'summaries:calculate {--student-id= : Hitung ulang untuk satu student}';

    protected $description = 'Pre-calculate summary distribusi per mahasiswa (materialized view)';

    public function handle(): int
    {
        $query = Student::query()->select(['id'])->orderBy('id');

        if ($studentId = $this->option('student-id')) {
            $query->where('id', (int) $studentId);
        }

        $now = now();
        $total = 0;

        $query->chunk(500, function ($students) use ($now, &$total) {
            $studentIds = $students->pluck('id')->all();

            if ($studentIds === []) {
                return;
            }

            $aggregates = $this->aggregateForStudents($studentIds);

            $rows = [];

            foreach ($studentIds as $studentId) {
                $aggregate = $aggregates[$studentId] ?? null;

                $rows[] = [
                    'student_id' => $studentId,
                    'total_transactions' => $aggregate->total_transactions ?? 0,
                    'total_items_received' => $aggregate->total_items_received ?? 0,
                    'total_spend' => $aggregate->total_spend ?? 0,
                    'last_distribution_at' => $aggregate->last_distribution_at ?? null,
                    'last_calculated_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('student_summaries')->upsert(
                $rows,
                ['student_id'],
                [
                    'total_transactions',
                    'total_items_received',
                    'total_spend',
                    'last_distribution_at',
                    'last_calculated_at',
                    'updated_at',
                ]
            );

            $total += count($rows);
        });

        $this->info("Student summaries updated. ({$total} mahasiswa)");

        return self::SUCCESS;
    }

    /**
     * @param  array<int, int>  $studentIds
     * @return array<int, object>
     */
    private function aggregateForStudents(array $studentIds): array
    {
        return DistributionTransaction::query()
            ->leftJoin('distribution_items as di', 'distribution_transactions.id', '=', 'di.transaction_id')
            ->whereIn('distribution_transactions.student_id', $studentIds)
            ->whereNotIn('distribution_transactions.status', ['cancelled'])
            ->groupBy('distribution_transactions.student_id')
            ->select([
                'distribution_transactions.student_id',
                DB::raw('COUNT(DISTINCT distribution_transactions.id) as total_transactions'),
                DB::raw('COALESCE(SUM(di.quantity), 0) as total_items_received'),
                DB::raw('COALESCE(SUM(di.quantity * di.selling_price_at_distribution), 0) as total_spend'),
                DB::raw('MAX(distribution_transactions.pickup_time) as last_distribution_at'),
            ])
            ->get()
            ->keyBy('student_id')
            ->all();
    }
}
