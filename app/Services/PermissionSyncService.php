<?php

namespace App\Services;

use App\Support\PermissionCatalog;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSyncService
{
    public function sync(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionCatalog::allNames() as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $this->migrateLegacyPermissions();
    }

    protected function migrateLegacyPermissions(): void
    {
        $legacyMap = config('permissions.legacy_map', []);

        if ($legacyMap === []) {
            return;
        }

        DB::transaction(function () use ($legacyMap) {
            foreach ($legacyMap as $oldName => $newNames) {
                $oldPermission = Permission::query()
                    ->where('name', $oldName)
                    ->where('guard_name', 'web')
                    ->first();

                if (! $oldPermission) {
                    continue;
                }

                $newNames = (array) $newNames;

                foreach ($newNames as $newName) {
                    $newPermission = Permission::firstOrCreate([
                        'name' => $newName,
                        'guard_name' => 'web',
                    ]);

                    $roleIds = DB::table('role_has_permissions')
                        ->where('permission_id', $oldPermission->id)
                        ->pluck('role_id');

                    foreach ($roleIds as $roleId) {
                        DB::table('role_has_permissions')->insertOrIgnore([
                            'permission_id' => $newPermission->id,
                            'role_id' => $roleId,
                        ]);
                    }

                    $userIds = DB::table('model_has_permissions')
                        ->where('permission_id', $oldPermission->id)
                        ->where('model_type', \App\Models\User::class)
                        ->pluck('model_id');

                    foreach ($userIds as $userId) {
                        DB::table('model_has_permissions')->insertOrIgnore([
                            'permission_id' => $newPermission->id,
                            'model_type' => \App\Models\User::class,
                            'model_id' => $userId,
                        ]);
                    }
                }

                DB::table('role_has_permissions')->where('permission_id', $oldPermission->id)->delete();
                DB::table('model_has_permissions')->where('permission_id', $oldPermission->id)->delete();
                $oldPermission->delete();
            }
        });

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
