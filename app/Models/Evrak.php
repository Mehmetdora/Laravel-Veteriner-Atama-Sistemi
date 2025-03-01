<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evrak extends Model
{



    public function evrak_tur()
    {
        return $this->belongsTo(EvrakTur::class);
    }

    public function vet_adi()
    {
        return $this->veteriner->name;
    }

    public function veteriner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function evrak_durumu()
    {
        return $this->hasOne(EvrakDurum::class);
    }

    public function urun()
    {
        return $this->belongsTo(Urun::class);
    }
}
