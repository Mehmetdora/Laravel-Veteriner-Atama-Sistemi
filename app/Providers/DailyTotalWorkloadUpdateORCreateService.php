<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Workload;
use App\Models\DailyTotalWorkload;

class DailyTotalWorkloadUpdateORCreateService{
    private $workloadCoefficients = [
        'ithalat' => 20,
        'transit' => 5,
        'antrepo_giris' => 5,
        'antrepo_varis' => 1,
        'antrepo_sertifika' => 2,
        'antrepo_cikis' => 5,
        'canli_hayvan' => 10,
    ];

    /*

        Günlük gelen tüm evrak için her gün yeni bir kayıt oluşturularak her gün ne kadar evrak iş yükü
        geldiğinin bilgisini tutan kayıtların güncellenmesi ve oluşturulması

    */


    public function updateOrCreateTodayWorkload($evrak_type){

        $todayWithHour = now()->setTimezone('Europe/Istanbul'); // tam saat
        $today = $todayWithHour->format('Y-m-d');

        $workload = $this->workloadCoefficients[$evrak_type];

        //DailyTotalWorkload oluşturma - güncelleme
        $daily_total_workload = DailyTotalWorkload::where('day',$today)->first();

        if($todayWithHour->hour <= 16){  // Gün içinde gelen bir evrak
            if(!$daily_total_workload){
                $daily_total_workload = new DailyTotalWorkload;
                $daily_total_workload->day = $today;
                $daily_total_workload->total_workload = $workload;
                $daily_total_workload->nobet_time = 0;
                $daily_total_workload->day_time = $workload;
                $daily_total_workload->save();
            }else{
                $daily_total_workload->total_workload += $workload;
                $daily_total_workload->day_time += $workload;
                $daily_total_workload->save();
            }


        }else{  // Nöbet zamanı gelen bir evrak
            if(!$daily_total_workload){
                $daily_total_workload = new DailyTotalWorkload;
                $daily_total_workload->day = $today;
                $daily_total_workload->total_workload = $workload;
                $daily_total_workload->nobet_time = $workload;
                $daily_total_workload->day_time = 0;
                $daily_total_workload->save();
            }else{
                $daily_total_workload->total_workload += $workload;
                $daily_total_workload->nobet_time += $workload;
                $daily_total_workload->save();
            }
        }

    }

}
