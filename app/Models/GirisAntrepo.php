<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GirisAntrepo extends Model
{
    protected $fillable = ['name'];

    public function evrak_antrepo_giris(){
        return $this->belongsTo(EvrakAntrepoGiris::class);
    }
}
