<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvrakAntrepoVaris extends Model
{


    protected $casts = [
        'urunKG' => 'decimal:3', // gelen verinin her zaman virgülden sonra 3 basamağı tutuldun,decimal
    ];

    public function evrak_adi()
    {
        return "Antrepo Varış";
    }



    public function veteriner()
    {
        return $this->morphOne(UserEvrak::class, 'evrak');
    }

    public function evrak_durumu()
    {
        return $this->morphOne(EvrakDurum::class, 'evrak');
    }


    public function saglikSertifikalari()
    {
        return $this->morphToMany(SaglikSertifika::class, 'evrak', 'evrak_saglik_sertifika');
    }
}
