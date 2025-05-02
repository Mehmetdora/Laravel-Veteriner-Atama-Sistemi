<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Telafi;

class YearWorklaodOrtalamasınıBulma{




    public function DigerVetsOrtalamaYearWorklaodDegeri(){


        /*

        Veterinerler arasında bu gün için telafisi ve izini olmayanlar seçilir, bu veterinerlerin
        year_workload değerlerinin ortalaması alınarak dönülüyor.

        */

        $simdikiZaman = now()->setTimezone('Europe/Istanbul'); // tam saat
        $today_yıl_ay_gun = $simdikiZaman->format('Y-m-d');

        $vets = User::role('veteriner')->where('status',1)
        ->whereDoesntHave('izins', function ($sorgu) use ($simdikiZaman) {  // Şu an izini olmayan veterinerler
            $sorgu->where('startDate', '<=', $simdikiZaman)
                ->where('endDate', '>=', $simdikiZaman);
        })->whereDoesntHave('workloads', function ($sorgu) use ($today_yıl_ay_gun){
            $sorgu->whereHas('telafis', function ($alt_sorgu) use ($today_yıl_ay_gun){
                $alt_sorgu->where('tarih',$today_yıl_ay_gun);
            });
        })
        ->get();


        $toplam_year_workload = 0;
        $toplam_vets_count = count($vets);
        $ortalama_year_workload = 0;

        foreach ($vets as $vet) {
            $workload = $vet->veterinerinBuYilkiWorkloadi();
            $toplam_year_workload += $workload->year_workload;
        }

        $ortalama_year_workload = (int)($toplam_year_workload/$toplam_vets_count);



        return $ortalama_year_workload;

    }

}
