<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    protected $fillable = [
        'personnel_id',
        'course_name',
        'score',
        'evaluator',
        'comments',
        'date',
    ];

    public function personnel(): BelongsTo
    {
        return $this->belongsTo(MilitaryPersonnel::class, 'personnel_id');
    }
}
