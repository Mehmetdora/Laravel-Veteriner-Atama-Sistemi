<?php

namespace App\Providers;

use App\Models\DailyTotalWorkload;

class OrtalamaGunlukWorkloadDegeriBulma
{



    public function ortalamaWorkloadHesapla()
    {

        // Ortalama günlük gelen evrak workloadları toplamalarını bulabilmek
        //için bugünden önceki hafta içi günlerden 5 gün kontrol edilerek
        //bu günler için DailyTotalWorkload değeri tutuluyormu diye bakılarak bu
        // kayıtlar bir arrayde tutulur


        $gunler = [];
        $today = now()->setTimezone('Europe/Istanbul');
        $checkedDays = 0;
        $addedDays = 0;

        while ($addedDays < 5) {
            // Bir gün geriye git
            $day = $today->copy()->subDays(++$checkedDays);

            // Hafta içi mi kontrol et (1 = Pazartesi, 5 = Cuma)
            if ($day->isWeekday()) {
                $gun = DailyTotalWorkload::where('day', $day->format('Y-m-d'))->first();
                if ($gun) {
                    $gunler[] = $gun;
                }
                $addedDays++;
            }
        }

        if(count($gunler) == 0){
            return 50;
        }


        $son_gunlerde_gelen_toplam_workload = 0;
        foreach ($gunler as $item) {
            $son_gunlerde_gelen_toplam_workload += $item->total_workload ;
        }

        $ortalama = (int)$son_gunlerde_gelen_toplam_workload/count($gunler);

        return $ortalama;

    }
}
