<?php

namespace App\Models\Concerns;

use App\Services\ActivityLogger;

trait LogsActivity
{
    abstract public static function activityLogLabel(): string;

    abstract public static function activityLogName(): string;

    public static function bootLogsActivity(): void
    {
        static::created(function (self $model) {
            ActivityLogger::modelEvent(
                $model,
                'created',
                $model->activityLogSubjectLabel(),
                static::activityLogName()
            );
        });

        static::updated(function (self $model) {
            if ($model->wasChanged()) {
                ActivityLogger::modelEvent(
                    $model,
                    'updated',
                    $model->activityLogSubjectLabel(),
                    static::activityLogName()
                );
            }
        });

        static::deleted(function (self $model) {
            ActivityLogger::modelEvent(
                $model,
                'deleted',
                $model->activityLogSubjectLabel(),
                static::activityLogName()
            );
        });
    }

    protected function activityLogSubjectLabel(): string
    {
        $name = $this->attributes['name'] ?? null;

        if (is_string($name) && $name !== '') {
            return static::activityLogLabel()." «{$name}»";
        }

        return static::activityLogLabel()." #{$this->getKey()}";
    }
}
