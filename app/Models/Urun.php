<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Urun extends Model
{
    protected $fillable = ['name'];

    public function evrak()
    {
        return $this->hasOne(Evrak::class);
    }
    public function evraks_ithalat()
    {
        return $this->morphToMany(EvrakIthalat::class, 'evrak', 'urun_evrak');
    }
    public function evraks_transit()
    {
        return $this->morphToMany(EvrakTransit::class, 'evrak', 'urun_evrak');
    }
    public function evraks_giris()
    {
        return $this->morphToMany(EvrakAntrepoGiris::class, 'evrak', 'urun_evrak');
    }
    public function evraks_varis()
    {
        return $this->morphToMany(EvrakAntrepoVaris::class, 'evrak', 'urun_evrak');
    }
    public function evraks_sertifika()
    {
        return $this->morphToMany(EvrakAntrepoSertifika::class, 'evrak', 'urun_evrak');
    }
    public function evraks_cikis()
    {
        return $this->morphToMany(EvrakAntrepoCikis::class, 'evrak', 'urun_evrak');
    }
}
