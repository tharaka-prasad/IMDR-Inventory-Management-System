<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, HasUserStamps, Auditable;

    protected $fillable = [
        'name', 'code', 'asset_type', 'description', 'created_by', 'updated_by',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
