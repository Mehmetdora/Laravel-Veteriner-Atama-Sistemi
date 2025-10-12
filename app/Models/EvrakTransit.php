<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvrakTransit extends Model
{

    protected $casts = [
        'urunKG' => 'decimal:3', // gelen verinin her zaman virgülden sonra 3 basamağı tutuldun,decimal
        'gtipNo' => 'array', // gtip no alanının json -> array dönüşümü için
    ];
    public function evrak_adi()
    {
        return "Transit";
    }

    public function setUrun(Urun $urun)
    {
        // Önce eski ürünü siliyoruz
        $this->urun()->detach();

        // Yeni ürünü ekliyoruz
        $this->urun()->attach($urun->id);
    }
    public function veteriner()
    {
        return $this->morphOne(UserEvrak::class, 'evrak');
    }

    public function kaydeden()
    {
        return $this->belongsTo(User::class, 'kaydeden_kullanici_id');
    }

    public function evrak_durumu()
    {
        return $this->morphOne(EvrakDurum::class, 'evrak');
    }
    public function urun()
    {
        return $this->morphToMany(Urun::class, 'evrak', 'urun_evrak');
    }

    public function saglikSertifikalari()
    {
        return $this->morphToMany(SaglikSertifika::class, 'evrak', 'evrak_saglik_sertifika');
    }
}
