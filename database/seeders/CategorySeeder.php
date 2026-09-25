<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'IT Equipment', 'code' => 'IT', 'asset_type' => 'it_asset', 'description' => 'Laptops, desktops, servers, network gear'],
            ['name' => 'Furniture', 'code' => 'FUR', 'asset_type' => 'fixed_asset', 'description' => 'Desks, chairs, cabinets'],
            ['name' => 'Vehicles', 'code' => 'VEH', 'asset_type' => 'fixed_asset', 'description' => 'Company vehicles'],
            ['name' => 'Stationery & Consumables', 'code' => 'CON', 'asset_type' => 'consumable', 'description' => 'Paper, toner, cables, misc. supplies'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['code' => $category['code']], $category);
        }
    }
}
