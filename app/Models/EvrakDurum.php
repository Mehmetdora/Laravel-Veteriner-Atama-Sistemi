<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvrakDurum extends Model
{
    protected $fillable = ['isRead'];
    
    public function evrak(){
        return $this->morphTo();
    }
}
