<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceRecordsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        protected User $user,
        protected ?int $classId = null,
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
    ) {}

    public function query()
    {
        $classIds = $this->user->canAccessAllClasses()
            ? null
            : $this->user->assignedClassIds();

        return AttendanceRecord::query()
            ->with(['supervisor.schoolClass', 'session.schoolClass'])
            ->whereHas('session', function ($query) use ($classIds) {
                if ($this->classId) {
                    $query->where('school_class_id', $this->classId);
                } elseif ($classIds !== null) {
                    $query->whereIn('school_class_id', $classIds);
                }

                if ($this->dateFrom) {
                    $query->whereDate('date', '>=', $this->dateFrom);
                }

                if ($this->dateTo) {
                    $query->whereDate('date', '<=', $this->dateTo);
                }
            })
            ->join('attendance_sessions', 'attendance_records.attendance_session_id', '=', 'attendance_sessions.id')
            ->orderByDesc('attendance_sessions.date')
            ->select('attendance_records.*');
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'الفصل',
            'المشرف',
            'الهاتف',
            'الحالة',
            'سبب العذر',
            'يوجد مرفق',
        ];
    }

    public function map($record): array
    {
        return [
            $record->session->date->format('Y-m-d'),
            $record->session->schoolClass->name,
            $record->supervisor->name,
            $record->supervisor->phone ?? '—',
            $record->statusLabel(),
            $record->excuse_reason ?? '—',
            $record->excuse_attachment ? 'نعم' : 'لا',
        ];
    }

    public function title(): string
    {
        return 'سجل الحضور';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
