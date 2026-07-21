<?php

namespace App\Exports;

use App\Models\Supervisor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupervisorDetailExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected Supervisor $supervisor,
    ) {
        $this->supervisor->load([
            'schoolClass',
            'warnings.createdBy',
            'evaluations.evaluatedBy',
            'attendanceRecords.session',
        ]);
    }

    public function collection(): Collection
    {
        $rows = collect();

        $rows->push(['', '']);
        $rows->push(['البيانات الشخصية', '']);
        $rows->push(['الاسم', $this->supervisor->name]);
        $rows->push(['الهاتف', $this->supervisor->phone ?? '—']);
        $rows->push(['الفصل', $this->supervisor->schoolClass->name]);
        $rows->push(['الحالة', $this->supervisor->statusLabel()]);
        $rows->push(['أيام التدريب', $this->supervisor->total_training_days]);
        $rows->push(['أيام مخصومة', $this->supervisor->deducted_days]);
        $rows->push(['أيام فعلية', $this->supervisor->effectiveTrainingDays()]);
        $rows->push(['', '']);

        $rows->push(['ملخص الحضور', '']);
        $rows->push(['حاضر', $this->supervisor->presentDaysCount()]);
        $rows->push(['غائب', $this->supervisor->absentDaysCount()]);
        $rows->push(['متأخر', $this->supervisor->lateDaysCount()]);
        $rows->push(['غياب بعذر', $this->supervisor->excusedDaysCount()]);
        $rows->push(['', '']);

        $rows->push(['سجل الحضور', '', '', '']);
        $rows->push(['التاريخ', 'الحالة', 'سبب العذر', 'مرفق']);

        foreach ($this->supervisor->attendanceRecords->sortByDesc(fn ($r) => $r->session->date) as $record) {
            $rows->push([
                $record->session->date->format('Y-m-d'),
                $record->statusLabel(),
                $record->excuse_reason ?? '—',
                $record->excuse_attachment ? 'نعم' : 'لا',
            ]);
        }

        $rows->push(['', '']);

        $rows->push(['سجل الإنذارات', '', '', '', '']);
        $rows->push(['التاريخ', 'المستوى', 'السبب', 'بواسطة', 'خصم']);

        foreach ($this->supervisor->warnings as $warning) {
            $rows->push([
                $warning->created_at->format('Y-m-d'),
                $warning->warning_level,
                $warning->reason,
                $warning->createdBy->name,
                $warning->triggered_deduction ? '−14 يوم' : '—',
            ]);
        }

        if ($this->supervisor->evaluations->isNotEmpty()) {
            $rows->push(['', '']);
            $rows->push(['التقييمات', '', '', '']);
            $rows->push(['التاريخ', 'الدرجة', 'ملاحظات', 'المقيّم']);

            foreach ($this->supervisor->evaluations as $evaluation) {
                $rows->push([
                    $evaluation->created_at->format('Y-m-d'),
                    $evaluation->score,
                    $evaluation->notes ?? '—',
                    $evaluation->evaluatedBy->name,
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['استمارة مشرف — '.$this->supervisor->name, ''];
    }

    public function title(): string
    {
        return 'كارت المشرف';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
