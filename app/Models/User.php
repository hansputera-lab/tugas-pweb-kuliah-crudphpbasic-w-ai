<?php

namespace App\Models;

use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\Role;
use App\Domains\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
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

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function userRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isHRManager(): bool
    {
        return $this->role === 'hr_manager';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function canManageHR(): bool
    {
        return in_array($this->role, ['super_admin', 'hr_manager']);
    }

    public function hasPermissionTo(string $permission): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        return $this->userRoles()
            ->whereHas('permissions', fn($q) => $q->where('name', $permission))
            ->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        return $this->userRoles()
            ->whereHas('permissions', fn($q) => $q->whereIn('name', $permissions))
            ->exists();
    }

    public function getAllPermissions(): Collection
    {
        if ($this->role === 'super_admin') {
            return Permission::all();
        }

        return Permission::whereHas('roles', fn($q) => $q->whereIn('id',
            $this->userRoles()->pluck('roles.id')
        ))->get();
    }

    public function hasRole(string $role): bool
    {
        return $this->userRoles()->where('name', $role)->exists();
    }
}
