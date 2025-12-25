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

    public function all_temp_workloads_update()
    {

        $simdikiZaman = now()->setTimezone('Europe/Istanbul'); // tam saat
        $today_yıl_ay_gun = $simdikiZaman->format('Y-m-d');


        /**
         * Burada yapılan izinli olan veterinerleri bulmak, sonrasında bu veterinerlerin
         * izinden döndükten sonra diğerlerinin year_workload değerleri ile aynı seviyede
         * temp_workload a sahip olabilmeleri için temp_workload güncellemesini yapmak.
         *
         * Bu güncellemede izinde olmayan ve telafisi olmayan veterinerlerin year_workload değerlerinin
         * ortalaması bulunarak her temp_workload değeri bu ortalama ile güncellenir.
         */


        // Bugün için izinli olan tüm aktif veterinerler
        $vets = User::role('veteriner')
            ->where('status', 1)
            ->whereHas('izins', function ($sorgu) use ($simdikiZaman) {
                $sorgu->where('startDate', '<=', $simdikiZaman)
                    ->where('endDate', '>=', $simdikiZaman);
            })->with('workloads')->get();



        // Eğer izinli veteriner varsa bunların temp_workload değerini izin süreleri boyunca her
        // evrak kaydında güncelle, izinli olan yoksa devam et
        if (count($vets) > 0) {

            $ortalama_year_workload = $this->ortalama_year_worklaod_degeri_bulma->DigerVetsOrtalamaYearWorklaodDegeri();
            foreach ($vets as $vet) {
                $vet_workload = $vet->veterinerinBuYilkiWorkloadi();
                $vet_workload->temp_workload = $ortalama_year_workload;
                $vet_workload->save();
            }
        }
    }
}
