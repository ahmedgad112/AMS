<?php

namespace Database\Seeders;

use App\Services\PermissionSyncService;
use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionSyncService::class)->sync();

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $inspector = Role::firstOrCreate(['name' => 'inspector', 'guard_name' => 'web']);

        $superAdmin->syncPermissions(PermissionCatalog::allNames());

        $inspector->syncPermissions([
            'view-supervisors',
            'create-supervisors',
            'edit-supervisors',
            'import-supervisors',
            'print-supervisors',
            'view-attendance',
            'create-attendance-sessions',
            'save-attendance-records',
            'close-attendance-sessions',
            'view-warnings',
            'create-warnings',
            'view-reports',
            'export-reports',
        ]);
    }
}
