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

        $this->updateWorkload($vet, $evrak->difficulty);
        $this->adjustCompensation($vet, $evrak->difficulty);

        return $vet;
    }

    private function getActiveVets($date)
    {
        return User::whereDoesntHave('izins', function ($query) use ($date) {
            $query->where('startDate', '<=', $date)
                ->where('endDate', '>=', $date);
        })->get();
    }

    private function calculateCompensation($vets, $date)
    {
        foreach ($vets as $vet) {
            $sonİzin = $vet->izins()->latest()->first();

            if ($sonİzin && $sonİzin->endDate->diffInDays($date) <= 14) {
                $this->compensationPool[$vet->id] = $this->getCompensationData($vet, $sonİzin);
            }
        }
    }

    private function generateWeights($vets)
    {
        $weights = [];
        foreach ($vets as $vet) {
            $base = 1 / (1 + $vet->workloads()->current()->value('total_difficulty'));
            $comp = $this->compensationPool[$vet->id]['remaining'] ?? 0;
            $weights[$vet->id] = $base + ($comp * 0.5);
        }
        return $weights;
    }

    private function selectVet($weights)
    {
        $total = array_sum($weights);
        $random = mt_rand() / mt_getrandmax() * $total;

        foreach ($weights as $id => $weight) {
            $random -= $weight;
            if ($random <= 0) return User::find($id);
        }
    }


    private function getCompensationData($vet, $leave)
    {
        $daysMissed = $leave->end_date->diffInDays($leave->start_date);
        $avgWorkload = WorkLoad::whereBetween('created_at', [
            $leave->start_date->subDays(7),
            $leave->start_date
        ])->avg('total_difficulty');

        return [
            'total' => $avgWorkload * $daysMissed,
            'daily_quota' => ceil(($avgWorkload * $daysMissed) / 7),
            'remaining' => $avgWorkload * $daysMissed
        ];
    }


    private function updateWorkload($vet, $difficulty)
    {
        $vet->workloads()->updateOrCreate(
            ['is_current' => true],
            ['total_difficulty' => DB::raw("total_difficulty + $difficulty")]
        );
    }

    private function adjustCompensation($vet, $difficulty)
    {
        if (isset($this->compensationPool[$vet->id])) {
            $this->compensationPool[$vet->id]['remaining'] = max(
                0,
                $this->compensationPool[$vet->id]['remaining'] - $difficulty
            );
        }
    }
}
