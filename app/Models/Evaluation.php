<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use LogsActivity;

    public static function activityLogLabel(): string
    {
        return 'تقييم';
    }

    public static function activityLogName(): string
    {
        return 'evaluations';
    }

    protected function activityLogSubjectLabel(): string
    {
        $supervisor = $this->supervisor?->name ?? '#'.$this->supervisor_id;

        return static::activityLogLabel()." — {$supervisor} ({$this->score}/100)";
    }

    protected $fillable = [
        'supervisor_id',
        'score',
        'notes',
        'evaluated_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function evaluatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by_user_id');
    }
}
