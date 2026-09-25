<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItDetail extends Model
{
    protected $table = 'inventory_it_details';

    protected $fillable = [
        'inventory_id', 'device_name', 'hostname', 'cpu', 'ram', 'storage', 'gpu',
        'operating_system', 'mac_address', 'ip_address', 'monitor_size',
        'printer_type', 'printer_ip', 'software_installed', 'license_reference',
        'antivirus', 'it_notes',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
