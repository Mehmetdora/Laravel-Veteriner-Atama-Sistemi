<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaglikSertifika extends Model
{
    protected $fillable = ['ssn', 'toplam_miktar', 'kalan_miktar'];

    protected $casts = [
        'toplam_miktar' => 'decimal:3', // gelen verinin her zaman virgülden sonra 3 basamağı tutuldun,decimal
        'kalan_miktar' => 'decimal:3',
    ];

    public static function get_giris_varis_ss_with_ssn($ssn)
    {

        /*

        Alınan ssn numarası ile antrepo giriş veya varış(dış) evraklarındaki ss'larından aynı ssn 'na sahip olanı bulur.
        */
        $ss_saved = SaglikSertifika::where(function ($query) use ($ssn) {
            $query->whereHas('evraks_giris', function ($q) use ($ssn) {
                $q->where('ssn', $ssn);
            })->orWhereHas('evraks_varis_dis', function ($q) use ($ssn) {
                $q->where('ssn', $ssn);
            });
        })->with(['evraks_giris.veteriner.user', 'evraks_varis_dis.veteriner.user'])
            ->first();

        return $ss_saved;
    }

    public function evraks_canli_hayvan()
    {
        return $this->morphedByMany(EvrakCanliHayvan::class, 'evrak', 'evrak_saglik_sertifika');
    }
    public function evraks_transit()
    {
        return $this->morphedByMany(EvrakTransit::class, 'evrak', 'evrak_saglik_sertifika');
    }
    public function evraks_ithalat()
    {
        return $this->morphedByMany(EvrakIthalat::class, 'evrak', 'evrak_saglik_sertifika');
    }
    public function evraks_giris()
    {
        return $this->morphedByMany(EvrakAntrepoGiris::class, 'evrak', 'evrak_saglik_sertifika');
    }
    public function evraks_varis()
    {
        return $this->morphedByMany(EvrakAntrepoVaris::class, 'evrak', 'evrak_saglik_sertifika');
    }

    public function evraks_varis_dis()
    {
        return $this->morphedByMany(EvrakAntrepoVarisDis::class, 'evrak', 'evrak_saglik_sertifika');
    }


    public function evraks_sertifika()
    {
        return $this->morphedByMany(EvrakAntrepoSertifika::class, 'evrak', 'evrak_saglik_sertifika');
    }
}
