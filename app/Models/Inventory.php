<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes, HasUserStamps, Auditable;

    protected $table = 'inventories';

    protected $fillable = [
        'asset_code', 'qr_code', 'asset_type', 'category_id', 'location_id',
        'item_name', 'description', 'brand', 'model', 'serial_number',
        'quantity', 'available_quantity',
        'unit_cost', 'total_cost', 'supplier', 'invoice_number',
        'purchase_date', 'received_date', 'payment_method', 'payment_date',
        'warranty_start', 'warranty_end',
        'condition', 'status', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'purchase_date' => 'date',
            'received_date' => 'date',
            'payment_date' => 'date',
            'warranty_start' => 'date',
            'warranty_end' => 'date',
        ];
    }

    // Relationships ---------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function itDetail(): HasOne
    {
        return $this->hasOne(InventoryItDetail::class);
    }

    public function depreciation(): HasOne
    {
        return $this->hasOne(Depreciation::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    // Scopes ------------------------------------------------------------------

    public function scopeOfType($query, string $assetType)
    {
        return $query->where('asset_type', $assetType);
    }

    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->where('asset_type', 'consumable')->where('available_quantity', '<=', $threshold);
    }

    public function scopeWarrantyExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('warranty_end')
            ->whereBetween('warranty_end', [now(), now()->addDays($days)]);
    }

    // Accessors ---------------------------------------------------------------

    public function isAvailable(): bool
    {
        return $this->available_quantity > 0 && $this->status === 'active';
    }
}
