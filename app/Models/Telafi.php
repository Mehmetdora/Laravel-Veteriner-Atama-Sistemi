<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telafi extends Model
{
    public function izin() {
        return $this->belongsTo(Izin::class,'izin_id');
    }

    public function workload() {
        return $this->belongsTo(WorkLoad::class,'workload_id');
    }
}
