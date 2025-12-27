<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NobetList extends Model
{
    protected $fillable = ['start_date', 'end_date', 'list'];

    protected $casts = [
        'list' => 'array', // JSON otomatik array olarak gelir
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
