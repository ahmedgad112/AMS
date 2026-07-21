<?php

namespace App\Support;

class PermissionCatalog
{
    public static function groups(): array
    {
        return config('permissions.groups', []);
    }

    public static function allNames(): array
    {
        $names = [];

        foreach (self::groups() as $group) {
            foreach ($group['permissions'] as $name => $label) {
                $names[] = $name;
            }
        }

        return $names;
    }

    public static function label(string $permission): string
    {
        foreach (self::groups() as $group) {
            if (isset($group['permissions'][$permission])) {
                return $group['permissions'][$permission];
            }
        }

        return $permission;
    }

    public static function protectedRoles(): array
    {
        return config('permissions.protected_roles', []);
    }

    public static function isProtectedRole(string $roleName): bool
    {
        return in_array($roleName, self::protectedRoles(), true);
    }
}
