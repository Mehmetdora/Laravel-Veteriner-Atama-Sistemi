<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvrakAntrepoCikis extends Model
{

    public function evrak_adi()
    {
        return "Antrepo Çıkış";
    }

    public function getUsks()
    {
        $usks = UsksNo::find($this->usks_id);
        return $usks;
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

    public function evrak_durumu()
    {
        return $this->morphOne(EvrakDurum::class, 'evrak');
    }
    public function urun()
    {
        return $this->morphToMany(Urun::class, 'evrak', 'urun_evrak');
    }
}
