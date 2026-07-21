<?php

namespace App\Exports;

use App\Models\Supervisor;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupervisorsSummaryExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        protected User $user,
        protected ?int $classId = null,
    ) {}

    public function query()
    {
        return Supervisor::query()
            ->with('schoolClass')
            ->withCount([
                'attendanceRecords as present_count' => fn ($q) => $q->where('status', 'present'),
                'attendanceRecords as absent_count' => fn ($q) => $q->where('status', 'absent'),
                'attendanceRecords as late_count' => fn ($q) => $q->where('status', 'late'),
                'attendanceRecords as excused_count' => fn ($q) => $q->where('status', 'excused'),
                'warnings as warnings_count',
            ])
            ->when(! $this->user->canAccessAllClasses(), fn ($q) => $q->whereIn('school_class_id', $this->user->assignedClassIds()))
            ->when($this->classId, fn ($q) => $q->where('school_class_id', $this->classId))
            ->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'الاسم',
            'الهاتف',
            'الفصل',
            'كود الفصل',
            'أيام التدريب',
            'أيام مخصومة',
            'أيام فعلية',
            'حاضر',
            'غائب',
            'متأخر',
            'غياب بعذر',
            'إنذارات نشطة',
            'إجمالي الإنذارات',
            'الحالة',
        ];
    }

    public function map($supervisor): array
    {
        return [
            $supervisor->name,
            $supervisor->phone ?? '—',
            $supervisor->schoolClass->name,
            $supervisor->schoolClass->code,
            $supervisor->total_training_days,
            $supervisor->deducted_days,
            $supervisor->effectiveTrainingDays(),
            $supervisor->present_count,
            $supervisor->absent_count,
            $supervisor->late_count,
            $supervisor->excused_count,
            $supervisor->active_warnings_count,
            $supervisor->warnings_count,
            $supervisor->statusLabel(),
        ];
    }

    public function title(): string
    {
        return 'ملخص المشرفين';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
