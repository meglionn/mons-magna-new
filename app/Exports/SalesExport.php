<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Produk',
            'SKU',
            'Jumlah Terjual',
            'Pendapatan (IDR)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '70AD47']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ]);

        // Row height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Style untuk data rows
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:D' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        // Alignment untuk kolom numerik
        $sheet->getStyle('C2:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Number format
        $sheet->getStyle('C2:C' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('D2:D' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 15,
            'C' => 18,
            'D' => 20,
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}