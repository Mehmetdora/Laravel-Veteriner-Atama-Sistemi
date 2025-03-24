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
        $now = now()->setTimezone('Europe/Istanbul'); // tam saat

        if ($now->hour >= 17) {  // sabah kaçta normal saate dönülecek?

            // 17 den sonra izinli olmayıp nöbetçi olan veterinerler
            $veterinerler = User::role('veteriner')->with(['workloads', 'izins', 'nobets'])
                ->where('status', 1)
                ->whereDoesntHave('izins', function ($query) use ($now) {
                    $query->where('startDate', '<=', $now)
                        ->where('endDate', '>=', $now);
                })->whereHas('nobets', function ($sorgu) use ($now) {
                    $sorgu->where('date', $now->format('Y-m-d'));
                })->get();
        } else {

            // Normal bir şekilde gün içindeki saatler için tüm izinde olmayan veterinerler
            $veterinerler = User::role('veteriner')->with(['workloads', 'izins', 'nobets'])
                ->where('status', 1)
                ->whereDoesntHave('izins', function ($query) use ($now) {
                    $query->where('startDate', '<=', $now)
                        ->where('endDate', '>=', $now);
                })->get();
        }




        // 2. En düşük iş yüklü veteriner(ler)i bul
        $minWorkload = PHP_INT_MAX;
        $adayVeterinerler = collect();


        // Her veterinerin bu yıl için aldığı işler karşılaştırılarak en düşük olan(lar) adayVeterinerler arasında eklenir
        foreach ($veterinerler as $vet) {

            // veterinerinBuYilkiWorkloadi fonksiyonu ile veterinerin bu yıl için bir workload modeli varsa getirir, yoksa bu yıl için yeni bir tane oluşturur.
            $currentWorkload = $vet->veterinerinBuYilkiWorkloadi()->year_workload;

            if ($currentWorkload < $minWorkload) {
                $minWorkload = $currentWorkload;
                $adayVeterinerler = collect([$vet]);
            } elseif ($currentWorkload == $minWorkload) {
                $adayVeterinerler->push($vet);
            }
        }

        // 3. Rastgele bir veterineri seç
        $seciliVeteriner = $adayVeterinerler->random();


        // 4. Veterinerin iş yükünü güncelle
        $this->updateWorkload(
            $seciliVeteriner,
            $this->workloadCoefficients[$documentType]
        );

        return $seciliVeteriner;
    }

    private function updateWorkload(User $vet, int $coefficient)
    {
        $today = Carbon::now();

        // Bu fonksiyona gelmeden önce assignVet fonksiyonunda tüm veterinerlere bu yıl için kesin bir tane workload atanmış oluyor.
        $veteriner_bu_yilki_workloadi = $vet->workloads->where('year', $today->year)->first();

        $veteriner_bu_yilki_workloadi->year_workload += $coefficient;
        $veteriner_bu_yilki_workloadi->total_workload += $coefficient;
        $veteriner_bu_yilki_workloadi->save();
    }
}
