<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnProduct extends Model
{
    use HasFactory, HasUserStamps;

    protected $table = 'returns';

    protected $fillable = [
        'assignment_id', 'quantity', 'return_date', 'received_by', 'condition', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
