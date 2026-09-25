<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'table_name', 'record_id', 'action', 'old_values', 'new_values', 'ip_address', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Central place every part of the app writes audit trail entries
     * through, so the "every inventory action generates an audit log"
     * and "audit log for login" rules are enforced consistently.
     */
    public static function write($subject, string $action, ?array $old = null, ?array $new = null, ?int $userId = null): self
    {
        return static::create([
            'user_id' => $userId ?? Auth::id(),
            'table_name' => is_object($subject) ? $subject->getTable() : (string) $subject,
            'record_id' => is_object($subject) ? $subject->getKey() : null,
            'action' => $action,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
