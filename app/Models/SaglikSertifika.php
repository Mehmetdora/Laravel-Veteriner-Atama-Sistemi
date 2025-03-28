<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaglikSertifika extends Model
{
    protected $fillable = ['ssn','miktar'];

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
    public function evraks_sertifika()
    {
        return $this->morphedByMany(EvrakAntrepoSertifika::class, 'evrak', 'evrak_saglik_sertifika');
    }
    public function evraks_cikis()
    {
        return $this->morphedByMany(EvrakAntrepoCikis::class, 'evrak', 'evrak_saglik_sertifika');
    }

}
