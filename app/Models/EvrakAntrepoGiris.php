<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvrakAntrepoGiris extends Model
{

    public function evrak_adi(){
        return "Antrepo Giriş";
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

    public function giris_antrepo(){
        return $this->belongsTo(GirisAntrepo::class);
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
