<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Order matters: roles/permissions and users must exist before
     * categories/locations/inventory (which need created_by), and
     * inventory must exist before any sample assignments are made.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            LocationSeeder::class,
            InventorySeeder::class,
        ]);
    }
}
