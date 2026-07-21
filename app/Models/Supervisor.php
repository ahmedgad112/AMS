<?php

namespace App\Models;

use App\Support\PhoneNormalizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supervisor extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'school_class_id',
        'total_training_days',
        'deducted_days',
        'active_warnings_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_training_days' => 'integer',
            'deducted_days' => 'integer',
            'active_warnings_count' => 'integer',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function presentDaysCount(): int
    {
        return $this->attendanceRecords()->where('status', 'present')->count();
    }

    public function absentDaysCount(): int
    {
        return $this->attendanceRecords()->where('status', 'absent')->count();
    }

    public function lateDaysCount(): int
    {
        return $this->attendanceRecords()->where('status', 'late')->count();
    }

    public function excusedDaysCount(): int
    {
        return $this->attendanceRecords()->where('status', 'excused')->count();
    }

    public function effectiveTrainingDays(): int
    {
        return max(0, $this->total_training_days - $this->deducted_days);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'suspended' => 'موقوف',
            default => $this->status,
        };
    }

    public static function findByPhone(string $input): ?self
    {
        $normalized = PhoneNormalizer::normalize($input);

        if ($normalized === '') {
            return null;
        }

        $candidates = array_unique(array_filter([
            trim($input),
            $normalized,
            '+20'.substr($normalized, 1),
            '20'.substr($normalized, 1),
        ]));

        $supervisor = static::query()->whereIn('phone', $candidates)->first();

        if ($supervisor) {
            return $supervisor;
        }

        return static::query()
            ->whereNotNull('phone')
            ->get()
            ->first(fn (self $s) => PhoneNormalizer::normalize($s->phone) === $normalized);
    }
}
