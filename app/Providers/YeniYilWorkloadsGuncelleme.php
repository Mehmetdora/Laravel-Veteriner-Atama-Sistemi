<?php

namespace App\Providers;

use App\Models\User;
use App\Models\DailyTotalWorkload;

class YeniYilWorkloadsGuncelleme
{


    public function YeniYilWorkloadsGuncelleme()
    {

        $todayWithHour = now()->setTimezone('Europe/Istanbul'); // tam saat
        $today = $todayWithHour->format('Y-m-d');
        $last_saved_evraks_year = DailyTotalWorkload::latest()?->first()?->created_at?->addHours(3)->year ?? $todayWithHour->year; // modelde 3 saat geri gösterir


        if ($todayWithHour->year > $last_saved_evraks_year) { // yeni yılın ilk evrağı gelmiş, yeni workloadlar oluştur

            $vets = User::role('veteriner')->where('status',1)->get();

            foreach ($vets as $vet) {

                $past_years_workload = $vet->workloads()
                ->where('year', $last_saved_evraks_year)
                ->first();

                $workload = $vet->workloads()->create([
                    'year' => $todayWithHour->year,
                    'year_workload' => 0,
                    'total_workload' => $past_years_workload->total_workload
                ]);

            }


        }
    }
}
