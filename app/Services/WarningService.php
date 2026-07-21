<?php

namespace App\Services;

use App\Models\Supervisor;
use App\Models\User;
use App\Models\Warning;
use Illuminate\Support\Facades\DB;

class WarningService
{
    public const DEDUCTION_DAYS = 14;

    public function issueWarning(Supervisor $supervisor, string $reason, User $issuedBy): Warning
    {
        return DB::transaction(function () use ($supervisor, $reason, $issuedBy) {
            $supervisor = Supervisor::query()->lockForUpdate()->findOrFail($supervisor->id);

            $newCount = $supervisor->active_warnings_count + 1;
            $warningLevel = min($newCount, 3);
            $triggeredDeduction = false;

            if ($newCount >= 3) {
                $supervisor->deducted_days += self::DEDUCTION_DAYS;
                $supervisor->active_warnings_count = 0;
                $triggeredDeduction = true;
            } else {
                $supervisor->active_warnings_count = $newCount;
            }

            $supervisor->save();

            return Warning::create([
                'supervisor_id' => $supervisor->id,
                'reason' => $reason,
                'warning_level' => $warningLevel,
                'created_by_user_id' => $issuedBy->id,
                'triggered_deduction' => $triggeredDeduction,
            ]);
        });
    }
}
