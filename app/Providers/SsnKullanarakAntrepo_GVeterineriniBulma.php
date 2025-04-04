<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Workload;
use App\Models\SaglikSertifika;

class SsnKullanarakAntrepo_GVeterineriniBulma
{
    public function ssn_ile_antrepo_giris_vet_bul($formData,$i){
        // Evrağın atanacağı veteriner sağlık sertifikası üzerinden Antrepo Giriş türü evrağının atandığı veterinere atanmalı
        $ss_input = $formData[$i]['vetSaglikSertifikasiNo'][0];

        $ss_saved = SaglikSertifika::where('ssn', $ss_input['miktar'])
            ->with(['evraks_giris.veteriner.user']) // Gerekli ilişkileri tek sorguda çek
            ->first();

        $veteriner = $ss_saved?->evraks_giris?->first()?->veteriner?->user;

        if (!$veteriner) {
            throw new \Exception("Sağlık Sertifikası Numarası Bulunamadı, Sistemde Kayıtlı Olduğundan Emin Olduktan Sonra Tekrar Deneyiniz!");
        }

        return $veteriner;


    }
}
