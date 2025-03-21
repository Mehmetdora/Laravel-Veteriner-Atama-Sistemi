<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkLoad extends Model
{


    public function veteriner(){
        return $this->belongsTo(User::class);
    }
}
