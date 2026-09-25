<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depreciation extends Model
{
    protected $table = 'depreciation_details';

    protected $fillable = [
        'inventory_id', 'method', 'useful_life', 'accumulated_depreciation',
        'net_book_value', 'disposal_date', 'disposal_reason', 'disposal_value', 'remarks',
    ];

    protected function casts(): array
    {
        return [
            'accumulated_depreciation' => 'decimal:2',
            'net_book_value' => 'decimal:2',
            'disposal_value' => 'decimal:2',
            'disposal_date' => 'date',
        ];
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Recalculate accumulated depreciation and net book value automatically,
     * as required by the global rule "Net Book Value must calculate
     * automatically". Called by DepreciationService whenever the asset's
     * cost, method, or useful life change, or on a scheduled recalculation.
     */
    public function recalculate(): void
    {
        $cost = (float) $this->inventory->unit_cost * max(1, $this->inventory->quantity);
        $usefulLife = max(1, (int) $this->useful_life);
        $yearsElapsed = $this->inventory->purchase_date
            ? now()->diffInYears($this->inventory->purchase_date)
            : 0;
        $yearsElapsed = min($yearsElapsed, $usefulLife);

        if ($this->method === 'SLM') {
            $annualDepreciation = $cost / $usefulLife;
            $accumulated = $annualDepreciation * $yearsElapsed;
        } else { // WDV - Written Down Value, double-declining approximation
            $rate = 2 / $usefulLife;
            $remaining = $cost;
            for ($i = 0; $i < $yearsElapsed; $i++) {
                $remaining -= $remaining * $rate;
            }
            $accumulated = $cost - $remaining;
        }

        $accumulated = min($accumulated, $cost);

        $this->accumulated_depreciation = round($accumulated, 2);
        $this->net_book_value = round($cost - $accumulated, 2);
        $this->save();
    }
}
