<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvrakDurum extends Model
{
    public function evrak(){
        return $this->belongsTo(Evrak::class);
    }
}
