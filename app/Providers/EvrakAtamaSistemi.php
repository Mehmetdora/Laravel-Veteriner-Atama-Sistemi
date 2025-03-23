<?php

namespace App\Providers;


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
use App\Models\Izin;

class EvrakAtamaSistemi
{
    private $compensationPool = [];










    public function veterinereAtamaYap($type, $evrak_id)
    {

        $evrak = $this->evrakModeliniYukle($type, $evrak_id);

        $today = Carbon::now();
        $activeVets = $this->getActiveVets($today);
        //İzinde olmayan veterinerler alındı sadece

        $this->telafiHesapla($activeVets, $today);

        $weights = $this->agirliklariOlustur($activeVets);
        $vet = $this->veterinerSec($weights);

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

    private function telafiHesapla($vets, $date)
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

    private function calculateWorkloads($vets, Carbon $today)
    {
        $workloads = [];

        foreach ($vets as $vet) {

            $veterinerinSonWorkloadi = $vet->workloads->latest();

            if ($veterinerinSonWorkloadi != null) {   // İlk defa iş almışsa(yeni işe başlamışsa)
                $vet->workloads->create([
                    'year_workload' => 0,
                    'total_workload' => 0
                ]);
            } else {
                $veterinerinToplamYaptigiWorkload = $vet->workloads->sum('total_workloads');
                $veterinerinBuYilYaptigiWorkload = $veterinerinSonWorkloadi->year_workload;
            }


            /* Workload::where('vet_id', $vet->id)
                ->where('year_workload', $today->year)
                ->sum('total_difficulty'); */

            /* $workloads[$vet->id] = [
                'yearly' => $veterinerinBuYilYaptigiWorkload,
                'adjusted' => $this->calculateAdjustedWorkload($vet, $today)
            ]; */
        }

        return $workloads;
    }

    private function agirliklariOlustur($vets)
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
    private function veterinerSec($weights)
    {
        // En düşük ağırlığı bul
        $minWeight = min($weights);

        // En düşük ağırlığa sahip tüm veterinerleri seç
        $candidates = array_filter($weights, fn($w) => $w == $minWeight);

        // Rastgele bir veteriner seç
        return user::find(array_rand($candidates));
    }


    private function getCompensationData($active_vets, $izin, $telafiSuresi)
    {

        $today = Carbon::now();

        $izindenOnceCalisanVeterinerSayisi = User::role('veteriner')->all();
        $telafiSüresiBoyuncaKayitliIzinler = Izin::whereBetween('startDate', [$today, $today]);

        // İzin süresini hesapla
        $izinSuresi = $izin->endDate->diffInDays($izin->startDate);

        // İzin öncesi 10 günlük zaman aralığındaki günlük ortalama bir veterinere gelen ortalama iş yükünün değeri
        // Mesela ortalama günlük her veteriner 50 iş yükü değerinde iş yapmış
        $izindenOncekiDonemdeOrtalamaGunlukAtananIs = 0;
        $izindenOncekiDonemdeToplamAtananIs = 0;

        $toplam_evrak_ithalats = EvrakIthalat::whereBetween(
            'created_at',
            [$izin->startDate->subDays(10), $izin->startDate]
        );
        foreach ($toplam_evrak_ithalats as $evrak) {
            $izindenOncekiDonemdeToplamAtananIs += $evrak->difficulty_coefficient;
        }
        $toplam_evrak_transits = EvrakTransit::whereBetween(
            'created_at',
            [$izin->startDate->subDays(10), $izin->startDate]
        );
        foreach ($toplam_evrak_transits as $evrak) {
            $izindenOncekiDonemdeToplamAtananIs += $evrak->difficulty_coefficient;
        }
        $toplam_evrak_giriss = EvrakAntrepoGiris::whereBetween(
            'created_at',
            [$izin->startDate->subDays(10), $izin->startDate]
        );
        foreach ($toplam_evrak_giriss as $evrak) {
            $izindenOncekiDonemdeToplamAtananIs += $evrak->difficulty_coefficient;
        }
        $toplam_evrak_variss = EvrakAntrepoVaris::whereBetween(
            'created_at',
            [$izin->startDate->subDays(10), $izin->startDate]
        );
        foreach ($toplam_evrak_variss as $evrak) {
            $izindenOncekiDonemdeToplamAtananIs += $evrak->difficulty_coefficient;
        }
        $toplam_evrak_sertifikas = EvrakAntrepoSertifika::whereBetween(
            'created_at',
            [$izin->startDate->subDays(10), $izin->startDate]
        );
        foreach ($toplam_evrak_sertifikas as $evrak) {
            $izindenOncekiDonemdeToplamAtananIs += $evrak->difficulty_coefficient;
        }
        $toplam_evrak_cikiss = EvrakAntrepoCikis::whereBetween(
            'created_at',
            [$izin->startDate->subDays(10), $izin->startDate]
        );
        foreach ($toplam_evrak_cikiss as $evrak) {
            $izindenOncekiDonemdeToplamAtananIs += $evrak->difficulty_coefficient;
        }


        $izindenOncekiDonemdeOrtalamaGunlukAtananIs = ceil($izindenOncekiDonemdeToplamAtananIs / $izindenOnceCalisanVeterinerSayisi);

        /*  = $vet->workloads()->whereBetween('created_at', [
            $izin->startDate->subDays(10),
            $izin->startDate
        ])->avg('total_difficulty');
 */



        // Toplam kaçırılan iş yükü
        $toplamTelafiEdilmesiGerekenIsMiktari = $izindenOncekiDonemdeOrtalamaGunlukAtananIs * $izinSuresi;
        // Toplam 10 gün boyunca günlük 50 iş yükünden toplam izinden dönen veterinerin yetiştirmesi gereken 500 iş yükü değerinde iş birikmiş


        // Günlük telafi kotası
        // Veterinerin telafi etmesi gereken iş yüklerinin telafi etmesi gereken süreye bölerek bu süre içinde günlük extradan yapması gereken iş miktarı
        $extraOlarakYapilmasiGerekenGunlukIs = ceil($toplamTelafiEdilmesiGerekenIsMiktari / $telafiSuresi);

        return [
            'total' => $toplamTelafiEdilmesiGerekenIsMiktari,
            'daily_extra_workload' => $extraOlarakYapilmasiGerekenGunlukIs,
            'kalan_is_' => $toplamTelafiEdilmesiGerekenIsMiktari,
            'telafi_suresi' => $telafiSuresi
        ];
    }


    /* private function updateWorkload($vet, $difficulty_coefficient)
    {
        $vet->workloads()->updateOrCreate(
            ['is_current' => true],
            ['total_difficulty' => DB::raw("total_difficulty + $difficulty_coefficient")]
        );
    } */

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


    private function evrakModeliniYukle(string $tur, int $evrakId)
    {
        return match ($tur) {
            'EvrakIthalat' => EvrakIthalat::with(['urun', 'veteriner.kullanici', 'evrak_durumu'])->find($evrakId),
            'EvrakTransit' => EvrakTransit::with(['urun', 'veteriner.kullanici', 'evrak_durumu'])->find($evrakId),
            'EvrakAntrepoGiris' => EvrakAntrepoGiris::with(['urun', 'veteriner.kullanici', 'evrak_durumu'])->find($evrakId),
            'EvrakAntrepoVaris' => EvrakAntrepoVaris::with(['veteriner.kullanici', 'evrak_durumu'])->find($evrakId),
            'EvrakAntrepoSertifika' => EvrakAntrepoSertifika::with(['urun', 'veteriner.kullanici', 'evrak_durumu'])->find($evrakId),
            'EvrakAntrepoCikis' => EvrakAntrepoCikis::with(['urun', 'veteriner.kullanici', 'evrak_durumu'])->find($evrakId),
            default => throw new InvalidArgumentException("Geçersiz evrak türü: $tur")
        };
    }
}
