<?php

namespace App\Providers;

use App\Models\GemiIzni;
use App\Models\User;
use App\Models\Telafi;
use Carbon\Carbon;

class VetCanliHIzinKontrol
{
    public function izin_var_mi($vet_id)
    {
        $now = now()->setTimezone('Europe/Istanbul'); // tam saat

        $izinli = GemiIzni::where('veteriner_id', $vet_id)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>', $now)
            ->exists();

        if ($izinli) {
            return true;
        } else {
            return false;
        }
    }
}
