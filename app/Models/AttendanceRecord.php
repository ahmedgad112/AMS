<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'attendance_session_id',
        'supervisor_id',
        'status',
        'excuse_reason',
        'excuse_attachment',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'present' => 'حاضر',
            'absent' => 'غائب',
            'late' => 'متأخر',
            'excused' => 'غياب بعذر',
            default => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'present' => 'green',
            'absent' => 'red',
            'late' => 'yellow',
            'excused' => 'blue',
            default => 'gray',
        };
    }

    public function attachmentUrl(): ?string
    {
        if (! $this->excuse_attachment) {
            return null;
        }

        return Storage::disk('public')->url($this->excuse_attachment);
    }
}
