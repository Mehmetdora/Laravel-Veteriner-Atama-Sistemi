<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nobet extends Model
{
    protected $fillable = ['date'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
