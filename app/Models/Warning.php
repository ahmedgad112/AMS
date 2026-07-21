<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warning extends Model
{
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
