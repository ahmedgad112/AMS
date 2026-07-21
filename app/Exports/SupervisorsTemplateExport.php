<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupervisorsTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            ['أحمد محمد', '01111111111', 'ورشة شبكات - A'],
            ['سارة علي', '01111111112', 'ورشة برمجة - B'],
        ];
    }

    public function headings(): array
    {
        return ['الاسم', 'رقم التليفون', 'الفصل'];
    }

    public function title(): string
    {
        return 'المشرفين';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
