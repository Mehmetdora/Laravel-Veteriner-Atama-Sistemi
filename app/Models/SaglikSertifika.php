<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaglikSertifika extends Model
{
    protected $fillable = ['ssn', 'toplam_miktar', 'kalan_miktar'];

    protected $casts = [
        'toplam_miktar' => 'decimal:3', // gelen verinin her zaman virgülden sonra 3 basamağı tutuldun,decimal
        'kalan_miktar' => 'decimal:3',
    ];

    public function evraks_canli_hayvan()
    {
        return $this->morphedByMany(EvrakCanliHayvan::class, 'evrak', 'evrak_saglik_sertifika');
    }
    public function evraks_transit()
    {
        return $this->morphedByMany(EvrakTransit::class, 'evrak', 'evrak_saglik_sertifika');
    }
    public function evraks_ithalat()
    {
        return $this->morphedByMany(EvrakIthalat::class, 'evrak', 'evrak_saglik_sertifika');
    }
    public function evraks_giris()
    {
        return $this->morphedByMany(EvrakAntrepoGiris::class, 'evrak', 'evrak_saglik_sertifika');
    }
    public function evraks_varis()
    {
        return $this->morphedByMany(EvrakAntrepoVaris::class, 'evrak', 'evrak_saglik_sertifika');
    }

    public function evraks_varis_dis()
    {
        return $this->morphedByMany(EvrakAntrepoVarisDis::class, 'evrak', 'evrak_saglik_sertifika');
    }


    public function evraks_sertifika()
    {
        return $this->morphedByMany(EvrakAntrepoSertifika::class, 'evrak', 'evrak_saglik_sertifika');
    }
}
