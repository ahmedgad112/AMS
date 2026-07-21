<?php

namespace App\Exports;

use App\Models\Evaluation;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluationsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
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

        return Evaluation::query()
            ->with(['supervisor.schoolClass', 'evaluatedBy'])
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
            'الدرجة',
            'ملاحظات',
            'المقيّم',
        ];
    }

    public function map($evaluation): array
    {
        return [
            $evaluation->created_at->format('Y-m-d H:i'),
            $evaluation->supervisor->name,
            $evaluation->supervisor->schoolClass->name,
            $evaluation->score,
            $evaluation->notes ?? '—',
            $evaluation->evaluatedBy->name,
        ];
    }

    public function title(): string
    {
        return 'التقييمات';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
