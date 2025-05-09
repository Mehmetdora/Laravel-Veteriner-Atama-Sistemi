<?php

namespace App\Providers;

use App\Models\GemiIzni;
use App\Models\User;
use App\Models\Telafi;
use Illuminate\Support\Carbon;

class CanliHGemiIzniOlusturma
{


    public function canli_h_gemi_izin_olustur($vet_id, $start_date, $day_count)
    {
        $start = $start_date->copy();
        $end_date = $start_date->copy()->addDays($day_count);

        $gemi_izin = new GemiIzni;
        $gemi_izin->start_date = $start;
        $gemi_izin->end_date = $end_date;
        $gemi_izin->veteriner_id = $vet_id;
        $gemi_izin->save();
    }
}
