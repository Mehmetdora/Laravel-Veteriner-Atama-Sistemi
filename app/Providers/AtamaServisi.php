<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Workload;
use Carbon\Carbon;

class AtamaServisi
{


    protected $veteriner_evrak_durumu_kontrolu;

    function __construct(VeterinerEvrakDurumularıKontrolu $veterinerEvrakDurumularıKontrolu){

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
            if($this->veteriner_evrak_durumu_kontrolu->vet_evrak_durum_kontrol($vet->id)){
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
    }

    private function telafiHesapla(User $veteriner, Carbon $simdikiZaman)
    {
        // 10. Veterinerin Yük Kaydını Al
        $yukKaydi = $veteriner->veterinerinBuYilkiWorkloadi();

        // 11. Süresi Dolmuş Telafileri Temizle
        if ($yukKaydi->telafi_bitis_tarihi && $simdikiZaman->gt($yukKaydi->telafi_bitis_tarihi)) {
            $yukKaydi->update([
                'telafi_yuku' => 0,
                'telafi_bitis_tarihi' => null,
                'telafi_baslangic_tarihi' => null
            ]);
            return;
        }

        // 12. Yeni Telafi Gerekiyor mu Kontrol Et
        $sonIzin = $veteriner->izins()
            ->where('endDate', '<', $simdikiZaman)
            ->latest('endDate')
            ->first();

        if (!$sonIzin || $yukKaydi->telafi_bitis_tarihi) return;

        // 13. İzin Süresi Hesaplama
        $izinBaslangic = Carbon::parse($sonIzin->startDate);
        $izinBitis = Carbon::parse($sonIzin->endDate);
        $izinGunSayisi = $izinBitis->diffInDays($izinBaslangic) + 1;

        // 14. İzin Yılındaki Ortalama Yük
        $yil = $izinBaslangic->year;
        $yilToplamYuk = Workload::where('year', $yil)->sum('year_workload');
        $yilGunSayisi = $izinBitis->diffInDays(Carbon::create($yil, 1, 1)) + 1;
        $ortalamaGunlukYuk = $yilToplamYuk / max($yilGunSayisi, 1);

        // 15. Telafi Parametrelerini Kaydet
        $yukKaydi->update([
            'telafi_yuku' => ($ortalamaGunlukYuk * $izinGunSayisi) / $this->telafiSuresi,
            'telafi_baslangic_tarihi' => $simdikiZaman,
            'telafi_bitis_tarihi' => $simdikiZaman->copy()->addDays($this->telafiSuresi),
            'izin_baslangic_yili' => $yil
        ]);
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
