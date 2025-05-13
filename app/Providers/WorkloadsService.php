<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Telafi;

class WorkloadsService
{


    public function vet_işlemde_worklaod_count($vet_id)
    {
        $veteriner = User::with('evraks.evrak.evrak_durumu')->find($vet_id);
        $işlemde_workload_count = 0;

        if ($veteriner->evraks) {
            foreach ($veteriner->evraks as $evrak) {
                if($evrak->evrak->evrak_durumu->evrak_durum === 'İşlemde'){
                    $işlemde_workload_count += $evrak->difficulty_coefficient;
                }
            }
        }

        return $işlemde_workload_count;
    }
}
