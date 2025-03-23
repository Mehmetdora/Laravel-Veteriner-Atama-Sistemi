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

    public function evraks()
    {
        return $this->hasMany(UserEvrak::class, 'user_id');
    }

    public function izins()
    {
        return $this->belongsToMany(Izin::class, 'user_izin')->withPivot('startDate', 'endDate')
            ->withTimestamps();;
    }

    public function nobets()
    {
        return $this->hasMany(Nobet::class, 'user_id');
    }

    public function workloads()
    {
        return $this->hasMany(WorkLoad::class, 'vet_id');
    }

    public function unread_evraks_count()
    {
        return $this->evraks()
            ->whereHas('evrak', function ($query) {
                $query->whereHas('evrak_durumu', function ($subQuery) {
                    $subQuery->where('isRead', 0);
                });
            })
            ->count();
    }

    public function veterinerinBuYilkiWorkloadi()
    {

        // yıl bilgisi bu yıl olan veterinerin workloadi var mı bak varsa dön yoksa yeni bir tane oluştur

        $workload = $this->workloads()->firstOrCreate(
            ['year' => date('Y')],
            [
                'year' => date('Y'),
                'year_workload' => 0,
                'total_workload' => 0
            ]
        );



        return $workload->refresh();
    }
}
