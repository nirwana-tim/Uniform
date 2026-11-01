<?php

namespace App\Services;

use App\Imports\EligibilityImport;
use App\Imports\EntitlementImport;
use App\Imports\ItemImport;
use App\Imports\ItemPriceImport;
use App\Imports\StockOpnameImport;
use App\Imports\StockReceiveImport;
use App\Imports\StudentImport;
use App\Models\ImportBatch;
use App\Models\StockOpname;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ImportService
{
    public function processImport(string $type, string $filePath, int $userId, ?int $stockOpnameId = null): ImportBatch
    {
        $batch = null;
        $totalRows = 0;

        try {
            $batch = ImportBatch::create([
                'import_type' => $type,
                'file_name' => basename($filePath),
                'total_rows' => 0,
                'success_rows' => 0,
                'failed_rows' => 0,
                'status' => 'processing',
                'imported_by' => $userId,
            ]);

            $importer = $this->resolveImporter($type, $filePath, $stockOpnameId, $userId);

            Excel::import($importer, $filePath);

            $totalRows = (isset($importer) && method_exists($importer, 'getTotalRows')) ? $importer->getTotalRows() : 0;
            $successRows = (isset($importer) && method_exists($importer, 'getImportedCount'))
                ? $importer->getImportedCount()
                : $totalRows;

            $batch->update([
                'status' => 'completed',
                'total_rows' => $totalRows,
                'success_rows' => $successRows,
            ]);

            return $batch->fresh();
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorLog = collect($failures)->map(fn ($f) => [
                'row' => $f->row(),
                'attribute' => $f->attribute(),
                'errors' => $f->errors(),
            ])->toArray();

            $batch->update([
                'status' => 'failed',
                'total_rows' => (isset($importer) && method_exists($importer, 'getTotalRows')) && $importer->getTotalRows() > 0
                    ? $importer->getTotalRows()
                    : $totalRows,
                'failed_rows' => collect($failures)->pluck('row')->unique()->count(),
                'success_rows' => 0,
                'error_log' => $errorLog,
            ]);

            Log::error("Import {$type} failed", ['batch_id' => $batch->id, 'errors' => $errorLog]);

            return $batch->fresh();
        } catch (\Exception $e) {
            if ($batch) {
                $batch->update([
                    'status' => 'failed',
                    'error_log' => ['message' => $e->getMessage()],
                ]);
            }

            Log::error("Import {$type} exception", ['exception' => $e->getMessage()]);

            throw $e;
        }
    }

    protected function resolveImporter(string $type, string $filePath, ?int $stockOpnameId = null, ?int $userId = null): object
    {
        return match ($type) {
            'student' => new StudentImport,
            'eligibility' => new EligibilityImport,
            'item' => new ItemImport,
            'stock_opname' => $this->resolveStockOpnameImporter($stockOpnameId, $userId),
            'item_price' => new ItemPriceImport,
            'entitlement' => new EntitlementImport,
            'stock_receive' => new StockReceiveImport,
            default => throw new \InvalidArgumentException("Import type '{$type}' is not supported."),
        };
    }

    protected function resolveStockOpnameImporter(?int $stockOpnameId, ?int $userId): StockOpnameImport
    {
        if (! $stockOpnameId) {
            $batch = StockOpname::create([
                'reference_number' => 'IMP-'.now()->format('YmdHis'),
                'opname_date' => now()->toDateString(),
                'period' => now()->format('Y').'/'.(now()->format('y') + 1),
                'notes' => 'Auto-created from import',
                'status' => 'draft',
                'created_by' => $userId,
            ]);
            $stockOpnameId = $batch->id;
        }

        return new StockOpnameImport((int) $stockOpnameId);
    }
}
