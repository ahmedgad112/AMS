<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    use LogsActivity;

    public static function activityLogLabel(): string
    {
        return 'جلسة حضور';
    }

    public static function activityLogName(): string
    {
        return 'attendance';
    }

    protected function activityLogSubjectLabel(): string
    {
        $date = $this->date?->format('Y-m-d') ?? '—';
        $class = $this->schoolClass?->name ?? '—';

        return static::activityLogLabel()." {$date} — {$class}";
    }

    protected $fillable = [
        'date',
        'school_class_id',
        'created_by_user_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
