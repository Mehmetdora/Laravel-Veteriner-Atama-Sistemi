<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evrak extends Model
{



    public function evrak_tur_adi(){
        return EvrakTur::find($this->ithalatTür)->name;
    }

    public function vet_adi(){
        return $this->veteriner->name;
    }

    public function veteriner(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function evrak_durumu(){
        return $this->hasOne(EvrakDurum::class);
    }
}
