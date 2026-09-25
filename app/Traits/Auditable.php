<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Writes an AuditLog row for every create / update / delete on the model,
 * satisfying the global rule "every inventory action must generate an
 * audit log". Attach to any model whose changes should be traceable.
 *
 * Fine-grained business actions (issue, return, transfer, maintenance,
 * login) are logged explicitly via AuditLogService instead of relying on
 * this generic hook, so this trait is best suited to CRUD-style models
 * (Inventory, Category, Location, User, SystemSetting).
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLog::write($model, 'created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            AuditLog::write($model, 'updated', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            $action = method_exists($model, 'trashed') && $model->trashed() ? 'soft_deleted' : 'deleted';
            AuditLog::write($model, $action, $model->getOriginal(), null);
        });
    }
}
