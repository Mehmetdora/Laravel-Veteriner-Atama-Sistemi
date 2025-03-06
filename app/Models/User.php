<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Psy\TabCompletion\Matcher\FunctionDefaultParametersMatcher;

class User extends Authenticatable
{

    use HasRoles;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function evraks(){
        return $this->morphMany(UserEvrak::class, 'evrak');
    }

    public function izins(){
        return $this->belongsToMany(Izin::class,'user_izin')->withPivot('startDate', 'endDate')
        ->withTimestamps();;
    }

    public function nobets(){
        return $this->hasMany(Nobet::class,'user_id');
    }

    public function unread_evraks_count(){
        return $this->evraks()
        ->whereHas('evrak_durumu', function ($query) {
            $query->where('isRead', 0);
        })->count();
    }
}
