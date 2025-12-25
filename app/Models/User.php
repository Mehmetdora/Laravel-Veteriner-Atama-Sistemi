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
            ->withTimestamps();
    }

    public function nobets()
    {
        return $this->hasMany(Nobet::class, 'user_id');
    }

    public function gemi_izins()
    {
        return $this->hasMany(GemiIzni::class, 'veteriner_id');
    }

    public function workloads()
    {
        return $this->hasMany(WorkLoad::class, 'vet_id');
    }

    public function kaydi_yapilan_ithalat_evraklari()
    {
        return $this->hasMany(EvrakIthalat::class, 'id');
    }
    public function kaydi_yapilan_transit_evraklari()
    {
        return $this->hasMany(EvrakTransit::class, 'id');
    }
    public function kaydi_yapilan_giris_evraklari()
    {
        return $this->hasMany(EvrakAntrepoGiris::class, 'id');
    }
    public function kaydi_yapilan_varis_evraklari()
    {
        return $this->hasMany(EvrakAntrepoVaris::class, 'id');
    }
    public function kaydi_yapilan_varis_dis_evraklari()
    {
        return $this->hasMany(EvrakAntrepoVarisDis::class, 'id');
    }
    public function kaydi_yapilan_sertifika_evraklari()
    {
        return $this->hasMany(EvrakAntrepoSertifika::class, 'id');
    }
    public function kaydi_yapilan_cikis_evraklari()
    {
        return $this->hasMany(EvrakAntrepoCikis::class, 'id');
    }
    public function kaydi_yapilan_canli_h_evraklari()
    {
        return $this->hasMany(EvrakCanliHayvan::class, 'id');
    }
    public function kaydi_yapilan_canli_h_gemi_evraklari()
    {
        return $this->hasMany(EvrakCanliHayvanGemi::class, 'id');
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
        $year = date('Y');

        // Önce mevcut kaydı bulmaya çalış
        $workload = $this->workloads()
            ->where('year', $year)
            ->first();

        // Yoksa YENİ OLUŞTUR ve refresh et
        if (!$workload) {
            $workload = $this->workloads()->create([
                'year' => $year,
                'year_workload' => 0,
                'temp_workload' => 0,
                'total_workload' => 0
            ]);

            // İlişkinin tamamlandığından emin ol
            $this->load('workloads');
            $workload->refresh();
        }


        $workload->refresh();

        return $workload;
    }


    public function veterinerinBuYilkiWorkloadValue()
    {
        $workload = $this->veterinerinBuYilkiWorkloadi();
        if (!$workload) {
            throw new \Exception("Veterinerler arasından yıllık evrak puanları getirilirken beklenmedik bir hata oluştu: Hata Kodu - 003");
        }

        $todayWithHour = now()->setTimezone('Europe/Istanbul'); // tam saat
        $today = $todayWithHour->format('Y-m-d');

        $has_telafi = $workload->telafis()->where('tarih', $today)->exists();

        return $has_telafi ? $workload->temp_workload : $workload->year_workload;
    }
}
