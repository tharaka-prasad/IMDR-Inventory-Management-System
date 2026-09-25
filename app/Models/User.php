<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasUserStamps, Auditable;

    protected $guard_name = 'web';

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Role helpers ------------------------------------------------------

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAssignee(): bool
    {
        return $this->role === 'assignee';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Relationships -------------------------------------------------------

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'assigned_to');
    }

    public function issuedAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'given_by');
    }
}
