<?php

namespace App\Domains\Department\Models;

use App\Domains\Employee\Models\Employee;
use App\Domains\Position\Models\Position;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function activeEmployees(): HasMany
    {
        return $this->employees()->where('status', 'active');
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->count();
    }
}
