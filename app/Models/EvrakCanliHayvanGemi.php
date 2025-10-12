<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvrakCanliHayvanGemi extends Model
{

    public function evrak_adi()
    {
        return 'Canlı Hayvan(GEMİ)';
    }
    public function veteriner()
    {
        return $this->morphOne(UserEvrak::class, 'evrak');
    }
    public function kaydeden()
    {
        return $this->belongsTo(User::class, 'kaydeden_kullanici_id');
    }
    public function gemi_izin()
    {
        return $this->hasOne(GemiIzni::class, 'evrak_id');
    }

    public function evrak_durumu()
    {
        return $this->morphOne(EvrakDurum::class, 'evrak');
    }
}
