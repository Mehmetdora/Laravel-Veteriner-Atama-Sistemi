<?php

namespace App\Providers;

use App\Models\Telafi;
use App\Models\User;
use App\Models\Workload;
use Carbon\Carbon;

class AtamaServisi
{


    protected $veteriner_evrak_durumu_kontrolu;

    function __construct(VeterinerEvrakDurumularıKontrolu $veterinerEvrakDurumularıKontrolu)
    {

        $this->veteriner_evrak_durumu_kontrolu = $veterinerEvrakDurumularıKontrolu;
    }

    private $workloadCoefficients = [
        'ithalat' => 20,
        'transit' => 5,
        'antrepo_giris' => 5,
        'antrepo_varis' => 1,
        'antrepo_sertifika' => 2,
        'antrepo_cikis' => 5,
        'canli_hayvan' => 10,
    ];

    private $telafiSuresi = 60;

    public function assignVet(string $documentType)
    {

        $now = now()->setTimezone('Europe/Istanbul'); // tam saat

        // 1. Aktif Veterinerleri Alma
        $veterinerler = $this->aktifVeterinerleriGetir($now);





        $todayWithHour = now()->setTimezone('Europe/Istanbul'); // tam saat
        $today = $todayWithHour->format('Y-m-d');

        $has_telafi = false;
        $bitmemis_telafiler = [];

        foreach ($veterinerler as $vet) {


            // Veterinerler arasından seçerken elinde daha bitmemiş bir evrak olanları geç
            if ($this->veteriner_evrak_durumu_kontrolu->vet_evrak_durum_kontrol($vet->id)) {
                continue;
            }


            $workload = $vet->veterinerinBuYilkiWorkloadi();
            $has_telafi = $workload->telafis()->where('tarih', $today)->exists();
            if ($has_telafi) {

                $telafiler = $workload->telafis()->where('tarih', $today)->get();

                foreach ($telafiler as $telafi) {
                    if ($telafi->remaining_telafi_workload > 0) {
                        $bitmemis_telafiler[] = [
                            'telafi' => $telafi,
                            'vet_id' => $vet->id
                        ];
                    }
                }
            }
        }


        // Eğer bugün için izinli olmayan veterinerler arasından telafisi olanlar varsa
        // bu veterinerlerin telafileri bitene kadar öncelik bunlara verilecek ,
        // kimsenin telafisi yoksa yada telafilerini bitirmişler ise sistem normal atama işlemini yapar

        if (empty($bitmemis_telafiler)) {


            // 2. Her Veteriner İçin Telafi Hesapla
            /* foreach ($veterinerler as $veteriner) {
            $this->telafiHesapla($veteriner, $now);
            } */

            // 2. En düşük iş yüklü veteriner(ler)i bul
            $minWorkload = PHP_INT_MAX;
            $adayVeterinerler = collect();


            // Her veterinerin bu yıl için aldığı işler karşılaştırılarak en düşük olan(lar) adayVeterinerler arasında eklenir
            foreach ($veterinerler as $vet) {

                // Veterinerler arasından seçerken elinde daha bitmemiş bir evrak olanları geç
                if ($this->veteriner_evrak_durumu_kontrolu->vet_evrak_durum_kontrol($vet->id)) {
                    continue;
                }

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
        }else{

            //dd('bitmemiş telafisi olan veteriner var',$bitmemis_telafiler);

            $secilenTelafi = array_rand($bitmemis_telafiler);

            $vet_id = $bitmemis_telafiler[$secilenTelafi]['vet_id'];
            $telafi = $bitmemis_telafiler[$secilenTelafi]['telafi'];

            $seciliVeteriner = User::find($vet_id);

            $this->updateWorkload(
                $seciliVeteriner,
                $this->workloadCoefficients[$documentType]
            );
            $telafi->remaining_telafi_workload -= $this->workloadCoefficients[$documentType];
            $telafi->save();

            return $seciliVeteriner;

        }


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


    /**
     * Aktif Veterinerleri Getirir
     * - İzinli olmayanlar
     * - Saat 16'den sonra sadece nöbetçiler
     */
    private function aktifVeterinerleriGetir(Carbon $simdikiZaman)
    {
        // 8. Nöbet Kontrolü (17:00 sonrası)
        if ($simdikiZaman->hour >= 16) {
            return User::role('veteriner')
                ->where('status', 1)
                ->whereDoesntHave('izins', function ($sorgu) use ($simdikiZaman) {
                    $sorgu->where('startDate', '<=', $simdikiZaman)
                        ->where('endDate', '>=', $simdikiZaman);
                })
                ->whereHas('nobets', function ($sorgu) use ($simdikiZaman) {
                    $sorgu->where('date', $simdikiZaman->format('Y-m-d'));
                })->get();
        }

        // 9. Normal Çalışma Saatleri
        return User::role('veteriner')
            ->where('status', 1)
            ->whereDoesntHave('izins', function ($sorgu) use ($simdikiZaman) {
                $sorgu->where('startDate', '<=', $simdikiZaman)
                    ->where('endDate', '>=', $simdikiZaman);
            })->get();
    }
}
