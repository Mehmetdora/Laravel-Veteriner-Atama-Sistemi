<?php

use App\Models\User;
use App\Models\WorkLoad;
use App\Models\EvrakIthalat;
use App\Models\EvrakTransit;
use Illuminate\Support\Carbon;
use App\Models\EvrakAntrepoCikis;
use App\Models\EvrakAntrepoGiris;
use App\Models\EvrakAntrepoVaris;
use Illuminate\Support\Facades\DB;
use App\Models\EvrakAntrepoSertifika;

class EvrakAtamaSistemi
{
    private $compensationPool = [];



    public function veterinereAtamaYap($type, $evrak_id)
    {

        $type = explode("\\", $type);
        $type = end($type);

        if ($type == "EvrakIthalat") {
            $evrak = EvrakIthalat::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakTransit") {
            $evrak = EvrakTransit::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoGiris") {
            $evrak = EvrakAntrepoGiris::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoVaris") {
            $evrak = EvrakAntrepoVaris::with(['veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoSertifika") {
            $evrak = EvrakAntrepoSertifika::with(['urun', 'veteriner.user',  'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoCikis") {
            $evrak = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        }


        $today = Carbon::now();
        $activeVets = $this->getActiveVets($today);

        $this->calculateCompensation($activeVets, $today);

        $weights = $this->generateWeights($activeVets);
        $vet = $this->selectVet($weights);

        $this->updateWorkload($vet, $evrak->difficulty_coefficient);
        $this->adjustCompensation($vet, $evrak->difficulty_coefficient);

        return $vet;
    }

    private function getActiveVets(Carbon $date)
    {
    return User::whereDoesntHave('izins', function ($query) use ($date) {
        $query->where('startDate', '<=', $date)
            ->where('endDate', '>=', $date);
    })->role('veteriner')->get();
    }

    private function calculateCompensation($vets, $date)
    {
        foreach ($vets as $vet) {
            $sonİzin = $vet->izins()->latest();
    
            if ($sonİzin) {
                // İzin süresini hesapla
                $izinSuresi = $sonİzin->endDate->diffInDays($sonİzin->startDate);
                
                // Telafi süresini izin süresinin 2 katı olarak belirle
                $telafiSuresi = $izinSuresi * 2;
    
                // İzin bitişinden sonraki telafi süresi içinde mi?
                if ($sonİzin->endDate->diffInDays($date) <= $telafiSuresi) {
                    $this->compensationPool[$vet->id] = $this->getCompensationData($vet, $sonİzin, $telafiSuresi);
                }
            }
        }
    }

    private function generateWeights($vets)
    {
    $weights = [];
    foreach ($vets as $vet) {
        $workload = $vet->workloads()->current()->value('total_difficulty') ?? 0;
        
        // İş yükü sıfırsa, temel ağırlığı 1 yap
        $baseWeight = ($workload == 0) ? 1 : 1 / ($workload + 1);
        
        $comp = $this->compensationPool[$vet->id]['remaining'] ?? 0;
        $weights[$vet->id] = $baseWeight + ($comp * 0.5);
    }
    return $weights;
    }
    private function selectVet($weights)
    {
    // En düşük ağırlığı bul
    $minWeight = min($weights);
    
    // En düşük ağırlığa sahip tüm veterinerleri seç
    $candidates = array_filter($weights, fn($w) => $w == $minWeight);
    
    // Rastgele bir veteriner seç
    return user::find(array_rand($candidates));
    }


    private function getCompensationData($vet, $izin, $telafiSuresi)
{
    // İzin süresini hesapla
    $izinSuresi = $izin->endDate->diffInDays($izin->startDate);

    // İzin öncesi 7 günlük ortalama iş yükünü al
    $avgWorkload = Workload::whereBetween('created_at', [
        $izin->startDate->subDays(7),
        $izin->startDate
    ])->avg('total_difficulty');

    // Toplam kaçırılan iş yükü
    $totalMissedWorkload = $avgWorkload * $izinSuresi;

    // Günlük telafi kotası
    $dailyQuota = ceil($totalMissedWorkload / $telafiSuresi);

    return [
        'total' => $totalMissedWorkload,
        'daily_quota' => $dailyQuota,
        'remaining' => $totalMissedWorkload,
        'telafi_suresi' => $telafiSuresi
    ];
}


    private function updateWorkload($vet, $difficulty_coefficient)
    {
        $vet->workloads()->updateOrCreate(
            ['is_current' => true],
            ['total_difficulty' => DB::raw("total_difficulty + $difficulty_coefficient")]
        );
    }

    private function adjustCompensation($vet, $difficulty_coefficient)
{
    if (isset($this->compensationPool[$vet->id])) {
        // Kalan iş yükünü azalt
        $this->compensationPool[$vet->id]['remaining'] = max(
            0,
            $this->compensationPool[$vet->id]['remaining'] - $difficulty_coefficient
        );

        // Telafi süresi doldu mu?
        $telafiSuresi = $this->compensationPool[$vet->id]['telafi_suresi'];
        $gecenGun = Carbon::now()->diffInDays($vet->izins()->latest()->first()->endDate);

        if ($gecenGun >= $telafiSuresi) {
            // Telafi süresi doldu, havuzdan çıkar
            unset($this->compensationPool[$vet->id]);
        }
    }
}
}
