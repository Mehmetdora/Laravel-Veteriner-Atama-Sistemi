<?php

namespace App\Providers;

use App\Models\User;
use App\Models\DailyTotalWorkload;

class TelafiBoyuncaTempWorkloadGuncelleme
{

    protected $ortalama_year_worklaod_degeri_bulma;

    function __construct(YearWorklaodOrtalamasınıBulma $year_worklaod_ortalamasını_bulma)
    {

        $this->ortalama_year_worklaod_degeri_bulma = $year_worklaod_ortalamasını_bulma;

    }

    public function all_temp_workloads_update(){

        $simdikiZaman = now()->setTimezone('Europe/Istanbul'); // tam saat
        $today_yıl_ay_gun = $simdikiZaman->format('Y-m-d');

        $ortalama_year_workload = $this->ortalama_year_worklaod_degeri_bulma->DigerVetsOrtalamaYearWorklaodDegeri();


        // Bugün için telafisi olan aktif tüm veterinerler

        /* $vets = User::role('veteriner')->where('status',1)
        ->whereHas('workloads', function ($query) use ($today_yıl_ay_gun){
            $query->whereHas('telafis', function ($sub_query) use ($today_yıl_ay_gun){
                $sub_query->where('tarih',$today_yıl_ay_gun);
            });
        })->with('workloads')->get(); */


        // Bugün için izinli olan tüm aktif veterinerler
        $vets = User::role('veteriner')
        ->where('status', 1)
        ->whereHas('izins', function ($sorgu) use ($simdikiZaman) {
            $sorgu->where('startDate', '<=', $simdikiZaman)
                ->where('endDate', '>=', $simdikiZaman);
        })->with('workloads')->get();


        foreach ($vets as $vet) {
            $vet_workload = $vet->veterinerinBuYilkiWorkloadi();
            $vet_workload->temp_workload = $ortalama_year_workload;
            $vet_workload->save();
        }

    }


}
