<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsksNo extends Model
{

    protected $fillable = ['usks_no','miktar'];

    public function evrak_antrepo_sertifika(){
        return $this->belongsTo(EvrakAntrepoSertifika::class,'evrak_antrepo_sertifika_id');
    }



}
