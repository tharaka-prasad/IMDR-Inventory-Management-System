<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasUserStamps;

    protected $fillable = [
        'from_assignment_id', 'to_assignment_id', 'inventory_id',
        'from_user_id', 'to_user_id', 'quantity', 'transfer_date',
        'approved_by', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
        ];
    }

    public function fromAssignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'from_assignment_id');
    }

    public function toAssignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'to_assignment_id');
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
