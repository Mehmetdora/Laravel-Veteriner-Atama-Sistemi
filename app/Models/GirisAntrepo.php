<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GirisAntrepo extends Model
{
    protected $fillable = ['name'];

    public function evraks_antrepo_giris(){

        return $this->hasMany(EvrakAntrepoGiris::class);
    }


    // Static bir fonk yaparak direkt model üzerinden erişilebilir bir fonk haline geldi
    public static function actives(){
        return self::where('is_active',1)->get();
    }
}
