<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NobetHafta extends Model
{
    protected $fillable = [
        'weekName',
        'startOfWeek',
        'endOfWeek',
        'sun',
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat'
    ];

    protected $casts = [
        'sun' => 'array',
        'mon' => 'array',
        'tue' => 'array',
        'wed' => 'array',
        'thu' => 'array',
        'fri' => 'array',
        'sat' => 'array',
    ];
}
