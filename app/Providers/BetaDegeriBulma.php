<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Telafi;

class BetaDegeriBulma{


    public function beta_degeri_bul($day){

        $telafis = Telafi::where('tarih',$day)->get();
        $vets = User::role('veteriner')->where('status',1)->get();

        $total_telafi = 0;
        $beta = 0;
        $telafisi_olmayan_vet_sayisi = 0;

        // Telafisi olmayan veterinerlerin sayısını bulma
        foreach ($vets as $vet) {
            $workload = $vet->veterinerinBuYilkiWorkloadi();
            $has_telafi = $workload->telafis()->where('tarih',$day)->exists();
            if(!$has_telafi){
                $telafisi_olmayan_vet_sayisi += 1;
            }
        }


        foreach($telafis as $telafi){
            $total_telafi += $telafi->total_telafi_workload;
        }



        if($total_telafi != 0 && $telafisi_olmayan_vet_sayisi != 0){
            $beta = (int)($total_telafi/$telafisi_olmayan_vet_sayisi);
        }

        return $beta;
    }

}
