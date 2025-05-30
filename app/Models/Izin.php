<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{

    protected $fillable = ['name'];
    public function users(){
        return $this->belongsToMany(User::class,'user_izin')->withPivot('startDate', 'endDate')
        ->withTimestamps();;
    }


    public function telafis(){
        return $this->hasMany(Telafi::class);
    }
}
