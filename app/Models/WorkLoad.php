<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkLoad extends Model
{

    protected $fillable = ['year', 'year_workload', 'total_workload'];


    public function veteriner()
    {
        return $this->belongsTo(User::class);
    }


    public function telafis()
    {
        return $this->hasMany(Telafi::class, 'workload_id');
    }
}
