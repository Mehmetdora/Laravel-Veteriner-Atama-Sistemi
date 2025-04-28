<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AracPlakaKg extends Model
{

    protected $fillable = ['arac_plaka','evrak_ithalat_id','miktar'];
    public function aracs_evrak_ithalat(){
        return $this->belongsTo(EvrakIthalat::class,'evrak_ithalat_id');
    }
}
