<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class AssetCodeService
{
    /**
     * Build a unique asset code in the format {PREFIX}-{CATEGORY_CODE}-{SEQ},
     * e.g. IMDR-IT-001, IMDR-FUR-002. The sequence is scoped per category so
     * each asset type gets its own running number, and generation happens
     * inside a transaction/lock to stay unique under concurrent requests.
     */
    public function generate(Category $category): string
    {
        $settings = SystemSetting::current();
        $prefix = $settings->asset_prefix ?: 'IMDR';
        $categoryCode = strtoupper($category->code);

        return DB::transaction(function () use ($prefix, $categoryCode) {
            $last = Inventory::withTrashed()
                ->where('asset_code', 'like', "{$prefix}-{$categoryCode}-%")
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('asset_code');

            $nextSequence = 1;
            if ($last) {
                $parts = explode('-', $last);
                $lastNumber = (int) end($parts);
                $nextSequence = $lastNumber + 1;
            }

            $code = sprintf('%s-%s-%03d', $prefix, $categoryCode, $nextSequence);

            // Guard against any residual collision (e.g. manually-entered codes).
            while (Inventory::withTrashed()->where('asset_code', $code)->exists()) {
                $nextSequence++;
                $code = sprintf('%s-%s-%03d', $prefix, $categoryCode, $nextSequence);
            }

            return $code;
        });
    }
}
