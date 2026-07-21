<?php

namespace Database\Seeders;

use App\Services\PermissionSyncService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionSyncService::class)->sync();

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $inspector = Role::firstOrCreate(['name' => 'inspector', 'guard_name' => 'web']);

        $superAdmin->syncPermissions(\App\Support\PermissionCatalog::allNames());

        $inspector->syncPermissions([
            'manage-supervisors',
            'manage-attendance',
            'manage-warnings',
            'export-reports',
        ]);
    }
}
