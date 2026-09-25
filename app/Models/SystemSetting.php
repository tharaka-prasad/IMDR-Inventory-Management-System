<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use Auditable;

    protected $fillable = [
        'institute_name', 'logo_path', 'asset_prefix', 'qr_prefix', 'currency', 'updated_by',
    ];

    /**
     * There is always exactly one settings row. Fetch (or lazily create) it.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'institute_name' => config('app.name'),
            'asset_prefix' => 'IMDR',
            'qr_prefix' => 'IMDR-QR',
            'currency' => 'LKR',
        ]);
    }
}
