<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Workload;
use App\Models\SaglikSertifika;

class SsnKullanarakAntrepo_GVeterineriniBulma
{
    public function ssn_ile_antrepo_giris_vet_bul($formData, $i)
    {
        // Evrağın atanacağı veteriner sağlık sertifikası üzerinden Antrepo Giriş türü evrağının atandığı veterinere atanmalı
        $ss_input = $formData[$i]['vetSaglikSertifikasiNo'][0];

        $ss_saved = SaglikSertifika::where('ssn', $ss_input['ssn'])
            ->where('miktar', $ss_input['miktar'])
            ->first();



        if (!$ss_saved) {
            throw new \Exception("Sağlık Sertifikası bulunamadı.");
        }

        if (!$ss_saved->evraks_giris) {
            throw new \Exception("Sağlık Sertifikasına bağlı evrak girişi bulunamadı.");
        }

        if (!$ss_saved->evraks_giris?->first()?->veteriner?->user) {
            throw new \Exception("Evrak girişine bağlı veteriner bulunamadı.");
        }

        $veteriner = $ss_saved?->evraks_giris?->first()?->veteriner?->user;
        return $veteriner;

        
    }
}
