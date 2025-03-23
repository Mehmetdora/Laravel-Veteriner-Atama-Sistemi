<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Workload;
use Carbon\Carbon;

class AtamaServisi
{
    private $workloadCoefficients = [
        'ithalat' => 1,
        'transit' => 1,
        'antrepo_giris' => 4,
        'antrepo_varis' => 2,
        'antrepo_sertifika' => 3,
        'antrepo_cikis' => 5
    ];

    public function assignVet(string $documentType)
    {
        // 1. Aktif veterinerleri al
        $veterinerler = User::role('veteriner')->with('workloads')->get();

        // 2. En düşük iş yüklü veteriner(ler)i bul
        $minWorkload = PHP_INT_MAX;
        $adayVeterinerler = collect();


        // Her veterinerin toplam aldığı işler karşılaştırılarak en düşük olan(lar) adayVeterinerler arasında eklenir
        foreach ($veterinerler as $vet) {

            $currentWorkload = $vet->veterinerinBuYilkiWorkloadi()->year_workload;

            if ($currentWorkload < $minWorkload) {
                $minWorkload = $currentWorkload;
                $adayVeterinerler = collect([$vet]);
            } elseif ($currentWorkload == $minWorkload) {
                $adayVeterinerler->push($vet);
            }
        }

        // 3. Rastgele bir veteriner seç
        $seciliVeteriner = $adayVeterinerler->random();

        //dd($seciliVeteriner,$seciliVeteriner->workloads);

        // 4. İş yükünü güncelle
        $this->updateWorkload(
            $seciliVeteriner,
            $this->workloadCoefficients[$documentType]
        );

        return $seciliVeteriner;
    }

    private function updateWorkload(User $vet, int $coefficient)
    {
        $today = Carbon::now();



        // Bu fonksiyona gelmeden önce assignVet fonksiyonunda tüm veterinerlere bu yıl için bir workload atanmış oluyor zaten

        //dd($vet->workloads);
        $veteriner_bu_yilki_workloadi = $vet->workloads->where('year', $today->year)->first();
        //dd($veteriner_bu_yilki_workloadi);


        // veterinere öncesinde bir evrak atanmış mı diye kontrol ederek ona göre yeni bir tane oluştur yada düzenle




        $veteriner_bu_yilki_workloadi->year_workload += $coefficient;
        $veteriner_bu_yilki_workloadi->total_workload += $coefficient;
        $veteriner_bu_yilki_workloadi->save();



    }
}
