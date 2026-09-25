<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    // NOTE: this model intentionally does NOT use the generic Auditable
    // trait, because issue/return/transfer actions are logged explicitly
    // (with richer context) via AuditLogService inside their controllers.
    use HasFactory, SoftDeletes, HasUserStamps;

    protected $fillable = [
        'inventory_id', 'assigned_to', 'quantity', 'issue_date', 'given_by',
        'remarks', 'status', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
        ];
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'given_by');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnProduct::class, 'assignment_id');
    }

    public function returnedQuantity(): int
    {
        return (int) $this->returns()->sum('quantity');
    }

    public function outstandingQuantity(): int
    {
        return max(0, $this->quantity - $this->returnedQuantity());
    }
}
