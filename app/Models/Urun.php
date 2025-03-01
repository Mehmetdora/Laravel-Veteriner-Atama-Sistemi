<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Urun extends Model
{
    protected $fillable = ['name'];

    public function evrak()
    {
        return $this->hasOne(Evrak::class);
    }
}
