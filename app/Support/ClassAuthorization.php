<?php

namespace App\Support;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ClassAuthorization
{
    public static function canAccessClass(User $user, SchoolClass|int $class): bool
    {
        if ($user->canAccessAllClasses()) {
            return true;
        }

        $classId = $class instanceof SchoolClass ? $class->id : $class;

        return in_array($classId, $user->assignedClassIds(), true);
    }

    public static function scopeAccessibleClasses(Builder $query, User $user): Builder
    {
        if ($user->canAccessAllClasses()) {
            return $query;
        }

        return $query->whereIn('id', $user->assignedClassIds());
    }

    public static function abortUnlessCanAccess(User $user, SchoolClass|int $class): void
    {
        if (! self::canAccessClass($user, $class)) {
            abort(403, 'ليس لديك صلاحية الوصول لهذا الفصل.');
        }
    }
}
