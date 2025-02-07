<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evrak extends Model
{

    public function evrak_adi(){
        return EvrakTur::find($this->ithalatTür)->name;
    }

    public function vet_adi(){
        return User::find($this->veterinerId)->name;
    }
}
