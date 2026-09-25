<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@imdr.lk'],
            [
                'full_name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'phone' => '0770000000',
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );
        $superAdmin->assignRole('super_admin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@imdr.lk'],
            [
                'full_name' => 'Operations Admin',
                'password' => Hash::make('password'),
                'phone' => '0770000001',
                'role' => 'admin',
                'status' => 'active',
                'created_by' => $superAdmin->id,
            ]
        );
        $admin->assignRole('admin');

        $assignee = User::firstOrCreate(
            ['email' => 'assignee@imdr.lk'],
            [
                'full_name' => 'Sample Assignee',
                'password' => Hash::make('password'),
                'phone' => '0770000002',
                'role' => 'assignee',
                'status' => 'active',
                'created_by' => $admin->id,
            ]
        );
        $assignee->assignRole('assignee');
    }
}
