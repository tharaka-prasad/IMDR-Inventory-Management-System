<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Head Office - Ground Floor', 'building' => 'Main Building', 'department' => 'Administration'],
            ['name' => 'Head Office - 2nd Floor', 'building' => 'Main Building', 'department' => 'IT Department'],
            ['name' => 'Warehouse', 'building' => 'Warehouse Block', 'department' => 'Logistics'],
            ['name' => 'Branch Office - Kandy', 'building' => 'Kandy Branch', 'department' => 'Operations'],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(['name' => $location['name']], $location);
        }
    }
}
