<?php

namespace App\Providers;

use App\Models\User;
use App\Models\DailyTotalWorkload;
use App\Models\Telafi;
use Illuminate\Support\Facades\DB;

class YeniYilWorkloadsGuncelleme
{



    // eğer yeni workload kayıtları oluşturulmuşsa true, oluşturulmamışsa false döner
    // tüm telafiler, eksi workloadlar geride kalır ve sistem yeni sıfırlanmış workloadlar üzerinden devam eder
    public function YeniYilWorkloadsGuncelleme()
    {

        $todayWithHour = now()->setTimezone('Europe/Istanbul'); // tam saat
        $last_saved_evraks_year = DailyTotalWorkload::latest()?->first()?->updated_at?->addHours(3)->year ?? $todayWithHour->year; // modelde 3 saat geri gösterir


        //Burada en son gelen evrağın tarihi ile şuanki yıl karşılaştırılarak buna göre yeni workload oluşturuluyor.
        if ($todayWithHour->year > $last_saved_evraks_year) {


            $vets = User::role('veteriner')->where('status', 1)->get();
            foreach ($vets as $vet) {

                $past_years_workload = $vet->workloads()
                    ->where('year', $last_saved_evraks_year)
                    ->first();


                // geçen sene için workload ı varsa bu seneye aktar,
                // eğer yoksa zaten ilk defa bu sene çalışmaya başlamıştır - sıfırdan başlat
                if ($past_years_workload != null) {
                    $vet->workloads()->create([
                        'year' => $todayWithHour->year,
                        'year_workload' => 0,
                        'temp_workload' => 0,
                        'total_workload' => $past_years_workload->total_workload
                    ]);
                } else {

                    $vet->workloads()->create([
                        'year' => $todayWithHour->year,
                        'year_workload' => 0,
                        'temp_workload' => 0,
                        'total_workload' => 0
                    ]);
                }
            }
        }
    }
}
