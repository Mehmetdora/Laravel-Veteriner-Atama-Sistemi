<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvrakTur extends Model
{
    protected $fillable = ['name'];

    public function evrak(){
        return $this->hasMany(Evrak::class);
    }
}
