<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weapon extends Model
{
    protected $fillable = [
        'serial',
        'type',
        'model',
        'condition',
        'assigned_to',
        'status',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(MilitaryPersonnel::class, 'assigned_to');
    }
}
