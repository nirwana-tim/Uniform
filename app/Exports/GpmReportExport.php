<?php

namespace App\Exports;

use App\Models\Item;
use App\Services\GpmService;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GpmReportExport extends BaseExport implements FromCollection, WithCustomStartCell, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    private int $row = 0;

    /**
     * @var array<int, array{ratio: float, revenue: float}>
     */
    private array $rowGpmMeta = [];

    private float $grandTotalRevenue = 0.0;

    private float $grandTotalHpp = 0.0;

    public function __construct(
        private ?string $period = null,
        private ?string $category = null,
        private ?string $startDate = null,
        private ?string $endDate = null,
        private ?int $itemId = null
    ) {}

    public function startCell(): string
    {
        return 'A4';
    }

    public function collection(): Enumerable
    {
        return app(GpmService::class)->calculateGpm(
            $this->period,
            $this->category,
            $this->startDate,
            $this->endDate,
            $this->itemId
        );
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Harga Jual Rata-rata (Rp)',
            'HPP Rata-rata (Rp)',
            'Qty Terjual',
            'Penjualan Bersih (Rp)',
            'Inventory Out / HPP (Rp)',
            'Laba Kotor (Rp)',
            'GPM (%)',
        ];
    }

    public function map($item): array
    {
        $this->row++;
        $excelRow = $this->dataStartRow() + $this->row - 1;

        $revenue = round((float) ($item['total_selling_price'] ?? 0), 2);
        $cost = round((float) ($item['total_hpp'] ?? 0), 2);
        $gpmRatio = isset($item['gpm_ratio'])
            ? (float) $item['gpm_ratio']
            : GpmService::calculateGpmRatio($cost, $revenue);

        $this->rowGpmMeta[$excelRow] = [
            'ratio' => $gpmRatio,
            'revenue' => $revenue,
        ];
        $this->grandTotalRevenue += $revenue;
        $this->grandTotalHpp += $cost;

        return [
            $this->row,
            (string) ($item['item_code'] ?? ''),
            (string) ($item['item_name'] ?? '-'),
            (string) ($item['category_name'] ?? '-'),
            round((float) ($item['selling_price'] ?? 0), 2),
            round((float) ($item['hpp'] ?? 0), 2),
            (int) ($item['qty_sold'] ?? 0),
            $revenue,
            $cost,
            '=H'.$excelRow.'-I'.$excelRow,
            '=IF(H'.$excelRow.'>0,1-(I'.$excelRow.'/H'.$excelRow.'),0)',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $colCount = 11;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'LAPORAN GPM / LABA KOTOR', $colCount);

        $filterParts = [$this->period ? 'Periode: '.$this->period : 'Semua Periode'];
        if ($this->startDate && $this->endDate) {
            $filterParts[] = 'Tanggal: '.$this->startDate.' s/d '.$this->endDate;
        } elseif ($this->startDate) {
            $filterParts[] = 'Tanggal Mulai: '.$this->startDate;
        } elseif ($this->endDate) {
            $filterParts[] = 'Tanggal Selesai: '.$this->endDate;
        }
        if ($this->category) {
            $filterParts[] = 'Kategori: '.$this->category;
        }
        if ($this->itemId) {
            $item = Item::find($this->itemId);
            if ($item) {
                $filterParts[] = 'Item: '.$item->name.' ('.$item->code.')';
            }
        }
        $filterParts[] = 'Rumus: GPM = 100% - (Inventory Out HPP / Penjualan Bersih)';
        $this->setSubtitle($sheet, implode(' | ', $filterParts), $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        if ($lastRow >= $dataStart) {
            $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

            for ($i = $dataStart; $i <= $lastRow; $i++) {
                $meta = $this->rowGpmMeta[$i] ?? null;

                if ($meta !== null && $meta['revenue'] > 0) {
                    $this->applyGpmFill($sheet, 'K'.$i, $meta['ratio']);
                }
            }

            $totalRow = $lastRow + 1;
            $this->applyTotalStyle($sheet, $totalRow, $colCount);

            $sheet->setCellValue('A'.$totalRow, 'TOTAL');
            $sheet->setCellValue('G'.$totalRow, '=SUM(G'.$dataStart.':G'.$lastRow.')');
            $sheet->setCellValue('H'.$totalRow, '=SUM(H'.$dataStart.':H'.$lastRow.')');
            $sheet->setCellValue('I'.$totalRow, '=SUM(I'.$dataStart.':I'.$lastRow.')');
            $sheet->setCellValue('J'.$totalRow, '=SUM(J'.$dataStart.':J'.$lastRow.')');
            $sheet->setCellValue('K'.$totalRow, '=IF(H'.$totalRow.'>0,1-(I'.$totalRow.'/H'.$totalRow.'),0)');

            if ($this->grandTotalRevenue > 0) {
                $totalGpmRatio = GpmService::calculateGpmRatio($this->grandTotalHpp, $this->grandTotalRevenue);
                $this->applyGpmFill($sheet, 'K'.$totalRow, $totalGpmRatio);
            }

            $this->setFormatRupiah($sheet, 'E', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'F', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'H', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'I', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'J', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'G', $dataStart, $totalRow);
            $this->setFormatPercentage($sheet, 'K', $dataStart, $totalRow);
        }

        $this->setColumnWidths($sheet, [
            'A' => 5, 'B' => 20, 'C' => 30, 'D' => 14,
            'E' => 20, 'F' => 18, 'G' => 14, 'H' => 20,
            'I' => 22, 'J' => 18, 'K' => 14,
        ]);

        $sheet->freezePane('A'.($headerRow + 1));

        return null;
    }

    private function applyGpmFill(Worksheet $sheet, string $cell, float $ratio): void
    {
        $rgb = match (true) {
            $ratio < 0.10 => 'FFE0E0',
            $ratio < 0.20 => 'FFF8E0',
            default => 'E0FFE0',
        };

        $sheet->getStyle($cell)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB($rgb);
    }
}
