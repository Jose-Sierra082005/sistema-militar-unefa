<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MilitaryPersonnel extends Model
{
    protected $table = 'military_personnel';

    protected $fillable = [
        'name',
        'cedula',
        'rank',
        'role',
        'status',
        'phone',
        'email',
    ];

    public function weapons(): HasMany
    {
        return $this->hasMany(Weapon::class, 'assigned_to');
    }

    public function guardShifts(): HasMany
    {
        return $this->hasMany(GuardShift::class, 'personnel_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'personnel_id');
    }
}
