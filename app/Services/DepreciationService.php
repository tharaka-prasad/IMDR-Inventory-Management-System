<?php

namespace App\Services;

use App\Models\Depreciation;
use App\Models\Inventory;

class DepreciationService
{
    /**
     * Create or update the depreciation record for an asset and recalculate
     * its net book value. Called from InventoryController on store/update
     * whenever depreciation fields are submitted, and from a scheduled
     * command (see console Kernel note) to keep NBV current over time.
     */
    public function sync(Inventory $inventory, array $data): Depreciation
    {
        $depreciation = $inventory->depreciation ?: new Depreciation(['inventory_id' => $inventory->id]);

        $depreciation->fill([
            'inventory_id' => $inventory->id,
            'method' => $data['method'] ?? $depreciation->method ?? 'SLM',
            'useful_life' => $data['useful_life'] ?? $depreciation->useful_life,
            'disposal_date' => $data['disposal_date'] ?? $depreciation->disposal_date,
            'disposal_reason' => $data['disposal_reason'] ?? $depreciation->disposal_reason,
            'disposal_value' => $data['disposal_value'] ?? $depreciation->disposal_value,
            'remarks' => $data['remarks'] ?? $depreciation->remarks,
        ]);
        $depreciation->save();
        $depreciation->refresh();
        $depreciation->setRelation('inventory', $inventory);
        $depreciation->recalculate();

        return $depreciation;
    }
}
