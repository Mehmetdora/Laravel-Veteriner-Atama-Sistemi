<?php

namespace App\Providers;

use App\Models\User;

class EvrakVeterineriDegisirseWorkloadGuncelleme
{

    private $workloadCoefficients = [
        'ithalat' => 20,
        'numuneli_ithalat' => 40,
        'transit' => 5,
        'antrepo_giris' => 5,
        'antrepo_varis' => 1,
        'antrepo_varis_dis' => 1,
        'antrepo_sertifika' => 2,
        'antrepo_cikis' => 5,
        'canli_hayvan' => 10,
    ];
    public function veterinerlerin_worklaods_guncelleme($old_vet_id, $new_vet_id, $evrak_type, $new_evrak_type)
    {

        $old_coefficient = $this->workloadCoefficients[$evrak_type];
        $new_coefficient = $this->workloadCoefficients[$new_evrak_type];

        $old_vet = User::find($old_vet_id);
        $new_vet = User::find($new_vet_id);

        $old_vet_worklaod = $old_vet->veterinerinBuYilkiWorkloadi();
        $new_vet_worklaod = $new_vet->veterinerinBuYilkiWorkloadi();

        if ($evrak_type != $new_evrak_type && $new_evrak_type == 'numuneli_ithalat') {

            if ($old_vet_id != $new_vet_id) { // veteriner değişmişse

                // eski veterinerin workload ı azalat , yenisini arttır
                $old_vet_worklaod->year_workload -= $old_coefficient;
                $old_vet_worklaod->total_workload -= $old_coefficient;
                if ($old_vet_worklaod->temp_workload != 0) {
                    $old_vet_worklaod->temp_workload -= $old_coefficient;
                }
                $old_vet_worklaod->save();

                $new_vet_worklaod->year_workload += $new_coefficient;
                $new_vet_worklaod->total_workload += $new_coefficient;
                if ($new_vet_worklaod->temp_workload != 0) {
                    $new_vet_worklaod->temp_workload += $new_coefficient;
                }
                $new_vet_worklaod->save();
            } else {        // veteriner aynı, evrak değişmişse
                $old_vet_worklaod->year_workload += 20;
                $old_vet_worklaod->total_workload += 20;
                if ($old_vet_worklaod->temp_workload != 0) {
                    $old_vet_worklaod->temp_workload += 20;
                }
                $old_vet_worklaod->save();
            }
        } elseif ($evrak_type != $new_evrak_type && $new_evrak_type == 'ithalat') {

            if ($old_vet_id != $new_vet_id) { // veteriner değişmişse
                $old_vet_worklaod->year_workload -= $old_coefficient;
                $old_vet_worklaod->total_workload -= $old_coefficient;
                if ($old_vet_worklaod->temp_workload != 0) {
                    $old_vet_worklaod->temp_workload -= $old_coefficient;
                }
                $old_vet_worklaod->save();

                $new_vet_worklaod->year_workload += $new_coefficient;
                $new_vet_worklaod->total_workload += $new_coefficient;
                if ($new_vet_worklaod->temp_workload != 0) {
                    $new_vet_worklaod->temp_workload += $new_coefficient;
                }
                $new_vet_worklaod->save();
            } else {
                $old_vet_worklaod->year_workload -= 20;
                $old_vet_worklaod->total_workload -= 20;
                if ($old_vet_worklaod->temp_workload != 0) {
                    $old_vet_worklaod->temp_workload -= 20;
                }
                $old_vet_worklaod->save();
            }
        } else {    // evrak türleri aynı , veterinerler değişiyor
            // Evrak ithalat tipi değişmemişse,sadece vet değiştir
            // Eski veterineri workload ını evrağın coefficieni kadar azaltma
            // yeni veterinerin worklaod değerini de bu kadar arttırma


            $old_vet_worklaod->year_workload -= $old_coefficient;
            $old_vet_worklaod->total_workload -= $old_coefficient;
            if ($old_vet_worklaod->temp_workload > 0) {
                $old_vet_worklaod->temp_workload -= $old_coefficient;
            }
            $old_vet_worklaod->save();


            $new_vet_worklaod->year_workload += $old_coefficient;
            $new_vet_worklaod->total_workload += $old_coefficient;
            if ($new_vet_worklaod->temp_workload > 0) {
                $new_vet_worklaod->temp_workload += $old_coefficient;
            }
            $new_vet_worklaod->save();
        }
    }
}
