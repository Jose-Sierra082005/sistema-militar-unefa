<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuardShift extends Model
{
    protected $fillable = [
        'personnel_id',
        'post',
        'shift_time',
        'date',
        'status',
    ];

    public function personnel(): BelongsTo
    {
        return $this->belongsTo(MilitaryPersonnel::class, 'personnel_id');
    }
}
