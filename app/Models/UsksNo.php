<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsksNo extends Model
{


    protected $fillable = ['usks_no', 'miktar'];

    protected $casts = [
        'miktar' => 'decimal:3', // gelen verinin her zaman virgülden sonra 3 basamağı tutuldun,decimal
    ];
    public function evrak_antrepo_sertifika()
    {
        return $this->belongsTo(EvrakAntrepoSertifika::class, 'evrak_antrepo_sertifika_id');
    }
}
