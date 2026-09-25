<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Services\AssetCodeService;
use App\Services\DepreciationService;
use App\Services\QrCodeService;
use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $assetCodeService = app(AssetCodeService::class);
        $qrCodeService = app(QrCodeService::class);
        $depreciationService = app(DepreciationService::class);

        $it = Category::where('code', 'IT')->first();
        $furniture = Category::where('code', 'FUR')->first();
        $consumables = Category::where('code', 'CON')->first();
        $location = Location::first();

        if (! $it || ! $furniture || ! $consumables || ! $location) {
            return;
        }

        $samples = [
            [
                'asset_type' => 'it_asset', 'category_id' => $it->id, 'item_name' => 'Dell Latitude 5440 Laptop',
                'brand' => 'Dell', 'model' => 'Latitude 5440', 'serial_number' => 'DL5440-0001',
                'quantity' => 5, 'unit_cost' => 285000, 'condition' => 'new',
                'it_details' => ['cpu' => 'Intel i5-1335U', 'ram' => '16GB', 'storage' => '512GB SSD', 'operating_system' => 'Windows 11 Pro'],
                'depreciation' => ['method' => 'SLM', 'useful_life' => 5],
            ],
            [
                'asset_type' => 'fixed_asset', 'category_id' => $furniture->id, 'item_name' => 'Executive Office Chair',
                'brand' => 'ErgoPlus', 'model' => 'EP-200', 'serial_number' => null,
                'quantity' => 10, 'unit_cost' => 32000, 'condition' => 'new',
                'depreciation' => ['method' => 'WDV', 'useful_life' => 8],
            ],
            [
                'asset_type' => 'consumable', 'category_id' => $consumables->id, 'item_name' => 'HP Toner Cartridge 12A',
                'brand' => 'HP', 'model' => '12A', 'serial_number' => null,
                'quantity' => 3, 'unit_cost' => 8500, 'condition' => 'new',
            ],
        ];

        foreach ($samples as $sample) {
            $category = Category::find($sample['category_id']);

            $inventory = Inventory::create([
                'asset_type' => $sample['asset_type'],
                'category_id' => $sample['category_id'],
                'location_id' => $location->id,
                'item_name' => $sample['item_name'],
                'brand' => $sample['brand'],
                'model' => $sample['model'],
                'serial_number' => $sample['serial_number'],
                'quantity' => $sample['quantity'],
                'available_quantity' => $sample['quantity'],
                'unit_cost' => $sample['unit_cost'],
                'total_cost' => $sample['unit_cost'] * $sample['quantity'],
                'condition' => $sample['condition'],
                'status' => 'active',
                'purchase_date' => now()->subMonths(3),
                'received_date' => now()->subMonths(3),
                'asset_code' => $assetCodeService->generate($category),
                'qr_code' => $qrCodeService->generateValue(),
            ]);

            if (! empty($sample['it_details'])) {
                $inventory->itDetail()->create($sample['it_details']);
            }

            if (! empty($sample['depreciation'])) {
                $depreciationService->sync($inventory, $sample['depreciation']);
            }
        }
    }
}
