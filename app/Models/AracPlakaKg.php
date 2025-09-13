<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AracPlakaKg extends Model
{

    protected $casts = [
        'miktar' => 'decimal:3', // gelen verinin her zaman virgülden sonra 3 basamağı tutuldun,decimal
    ];
    protected $fillable = ['arac_plaka', 'evrak_ithalat_id', 'miktar'];
    public function aracs_evrak_ithalat()
    {
        return $this->belongsTo(EvrakIthalat::class, 'evrak_ithalat_id');
    }
}
