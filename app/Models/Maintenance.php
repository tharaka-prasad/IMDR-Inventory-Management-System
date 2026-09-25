<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory, SoftDeletes, HasUserStamps;

    protected $table = 'maintenance';

    protected $fillable = [
        'inventory_id', 'issue_title', 'description', 'vendor', 'repair_cost',
        'sent_date', 'return_date', 'status', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'repair_cost' => 'decimal:2',
            'sent_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
