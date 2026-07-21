<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warning extends Model
{
    use LogsActivity;

    public static function activityLogLabel(): string
    {
        return 'إنذار';
    }

    public static function activityLogName(): string
    {
        return 'warnings';
    }

    protected function activityLogSubjectLabel(): string
    {
        $supervisor = $this->supervisor?->name ?? '#'.$this->supervisor_id;

        return static::activityLogLabel()." — {$supervisor} (مستوى {$this->warning_level})";
    }

    protected $fillable = [
        'supervisor_id',
        'reason',
        'warning_level',
        'created_by_user_id',
        'triggered_deduction',
    ];

    protected function casts(): array
    {
        return [
            'warning_level' => 'integer',
            'triggered_deduction' => 'boolean',
        ];
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
