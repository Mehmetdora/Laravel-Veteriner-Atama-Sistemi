<?php

namespace App\Models;

use App\Models\UserEvrak;
use App\Models\EvrakDurum;
use App\Models\SaglikSertifika;
use Illuminate\Database\Eloquent\Model;

class EvrakAntrepoVarisDis extends Model
{


    protected $casts = [
        'urunKG' => 'decimal:3', // gelen verinin her zaman virgülden sonra 3 basamağı tutuldun,decimal
    ];
    
    public function evrak_adi()
    {
        return "Antrepo Varış(DIŞ)";
    }

    public function giris_antrepo()
    {
        return $this->belongsTo(GirisAntrepo::class);
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
