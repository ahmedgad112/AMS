<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /** @var list<string> */
    protected static array $hiddenAttributes = [
        'password',
        'remember_token',
    ];

    public static function log(
        string $description,
        string $event = 'action',
        string $logName = 'system',
        ?Model $subject = null,
        array $properties = [],
    ): ActivityLog {
        return ActivityLog::create([
            'log_name' => $logName,
            'event' => $event,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'causer_id' => auth()->id(),
            'properties' => $properties === [] ? null : self::sanitizeProperties($properties),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    public static function modelEvent(Model $model, string $event, string $label, string $logName): ActivityLog
    {
        $description = match ($event) {
            'created' => "تم إضافة {$label}",
            'updated' => "تم تعديل {$label}",
            'deleted' => "تم حذف {$label}",
            default => "{$event}: {$label}",
        };

        $properties = [];

        if ($event === 'updated') {
            $changes = collect($model->getChanges())->except(self::$hiddenAttributes);
            if ($changes->isNotEmpty()) {
                $properties['changes'] = $changes->all();
                $properties['old'] = collect($model->getOriginal())
                    ->only($changes->keys()->all())
                    ->except(self::$hiddenAttributes)
                    ->all();
            }
        }

        if ($event === 'created') {
            $properties['attributes'] = collect($model->getAttributes())
                ->except(self::$hiddenAttributes)
                ->all();
        }

        if ($event === 'deleted') {
            $properties['attributes'] = collect($model->getAttributes())
                ->except(self::$hiddenAttributes)
                ->all();
        }

        return self::log($description, $event, $logName, $model, $properties);
    }

    protected static function sanitizeProperties(array $properties): array
    {
        return collect($properties)->map(function ($value) {
            if (! is_array($value)) {
                return $value;
            }

            return collect($value)->except(self::$hiddenAttributes)->all();
        })->all();
    }
}
