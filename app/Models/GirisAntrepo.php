<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GirisAntrepo extends Model
{
    protected $fillable = ['name'];

    public function evraks_antrepo_giris(){

        return $this->hasMany(EvrakAntrepoGiris::class);

        $evraks_antrepo_giris = EvrakAntrepoGiris::where('giris_antrepo_id',$this->id)->get();
        return $evraks_antrepo_giris;
    }
}
