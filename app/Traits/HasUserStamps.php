<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Automatically stamps created_by / updated_by on every model that uses it,
 * satisfying the global rule that every table must record who created and
 * who last updated each row.
 */
trait HasUserStamps
{
    public static function bootHasUserStamps(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = $model->created_by ?? Auth::id();
                if ($model->isFillable('updated_by') || array_key_exists('updated_by', $model->getAttributes())) {
                    $model->updated_by = $model->updated_by ?? Auth::id();
                }
            }
        });

        static::updating(function ($model) {
            if (Auth::check() && array_key_exists('updated_by', $model->getAttributes())) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
