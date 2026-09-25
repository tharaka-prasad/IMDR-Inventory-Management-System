<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes, HasUserStamps, Auditable;

    protected $fillable = [
        'name', 'building', 'department', 'created_by', 'updated_by',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
