<?php

namespace App\Providers;

use App\Models\User;

class EvrakVeterineriDegisirseWorkloadGuncelleme{

    private $workloadCoefficients = [
        'ithalat' => 20,
        'transit' => 5,
        'antrepo_giris' => 5,
        'antrepo_varis' => 1,
        'antrepo_sertifika' => 2,
        'antrepo_cikis' => 5,
        'canli_hayvan' => 10,
    ];
    public function veterinerlerin_worklaods_guncelleme($old_vet_id,$new_vet_id,$evrak_type){

        $coefficient = $this->workloadCoefficients[$evrak_type];

        $old_vet = User::find($old_vet_id);
        $new_vet = User::find($new_vet_id);

        $old_vet_worklaod = $old_vet->veterinerinBuYilkiWorkloadi();
        $new_vet_worklaod = $new_vet->veterinerinBuYilkiWorkloadi();



        // Eski veterineri workload ını evrağın coefficieni kadar azaltma
        // yeni veterinerin worklaod değerini de bu kadar arttırma

        $old_vet_worklaod->year_workload -= $coefficient;
        $old_vet_worklaod->total_workload -= $coefficient;
        if($old_vet_worklaod->temp_workload != 0){
            $old_vet_worklaod->temp_workload -= $coefficient;
        }
        $old_vet_worklaod->save();

        $new_vet_worklaod->year_workload += $coefficient;
        $new_vet_worklaod->total_workload += $coefficient;
        if($new_vet_worklaod->temp_workload != 0){
            $new_vet_worklaod->temp_workload += $coefficient;
        }
        $new_vet_worklaod->save();

    }


}
