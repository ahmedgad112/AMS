<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Warning;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WarningsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
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

        return Warning::query()
            ->with(['supervisor.schoolClass', 'createdBy'])
            ->when($this->classId, fn ($q) => $q->whereHas(
                'supervisor',
                fn ($sq) => $sq->where('school_class_id', $this->classId)
            ))
            ->when(! $this->classId && $classIds !== null, fn ($q) => $q->whereHas(
                'supervisor',
                fn ($sq) => $sq->whereIn('school_class_id', $classIds)
            ))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest();
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'المشرف',
            'الفصل',
            'مستوى الإنذار',
            'السبب',
            'بواسطة',
            'خصم 14 يوم',
        ];
    }

    public function map($warning): array
    {
        return [
            $warning->created_at->format('Y-m-d H:i'),
            $warning->supervisor->name,
            $warning->supervisor->schoolClass->name,
            $warning->warning_level,
            $warning->reason,
            $warning->createdBy->name,
            $warning->triggered_deduction ? 'نعم' : 'لا',
        ];
    }

    public function title(): string
    {
        return 'سجل الإنذارات';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
