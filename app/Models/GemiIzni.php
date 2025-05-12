<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GemiIzni extends Model
{
    public function veteriner()
    {
        return $this->belongsTo(User::class);
    }

    public function evrak()
    {
        return $this->belongsTo(EvrakCanliHayvanGemi::class,'evrak_id');
    }
}
