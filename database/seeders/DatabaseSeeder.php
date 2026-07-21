<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@attend.local'],
            [
                'name' => 'مدير النظام',
                'phone' => '01000000001',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['super-admin']);

        $inspector = User::firstOrCreate(
            ['email' => 'inspector@attend.local'],
            [
                'name' => 'مفتش تجريبي',
                'phone' => '01000000002',
                'password' => Hash::make('password'),
            ]
        );
        $inspector->syncRoles(['inspector']);

        $classA = SchoolClass::firstOrCreate(
            ['code' => 'IT-101'],
            ['name' => 'ورشة شبكات - A', 'location' => 'مبنى A - قاعة 101']
        );

        $classB = SchoolClass::firstOrCreate(
            ['code' => 'IT-102'],
            ['name' => 'ورشة برمجة - B', 'location' => 'مبنى B - قاعة 205']
        );

        $classA->inspectors()->syncWithoutDetaching([$inspector->id]);

        $supervisors = [
            ['name' => 'أحمد محمد', 'phone' => '01111111111', 'class' => $classA],
            ['name' => 'سارة علي', 'phone' => '01111111112', 'class' => $classA],
            ['name' => 'محمود حسن', 'phone' => '01111111113', 'class' => $classB],
        ];

        foreach ($supervisors as $data) {
            Supervisor::firstOrCreate(
                [
                    'name' => $data['name'],
                    'school_class_id' => $data['class']->id,
                ],
                [
                    'phone' => $data['phone'],
                    'total_training_days' => 30,
                    'status' => 'active',
                ]
            );
        }
    }
}
