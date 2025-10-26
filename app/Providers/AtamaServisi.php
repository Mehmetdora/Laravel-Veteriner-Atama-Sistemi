<?php

namespace App\Providers;

use App\Models\Telafi;
use App\Models\User;
use App\Models\Workload;
use Carbon\Carbon;


class AtamaServisi
{


    protected $veteriner_evrak_durumu_kontrolu;
    protected $temp_workloads_updater;
    protected $vet_gemi_izin_kontrolu;
    protected $worklaod_service;

    function __construct(WorkloadsService $workloadsService, VetCanliHIzinKontrol $vetCanliHIzinKontrol, TelafiBoyuncaTempWorkloadGuncelleme $telafi_boyunca_temp_workload_guncelleme, VeterinerEvrakDurumularıKontrolu $veterinerEvrakDurumularıKontrolu)
    {
        $this->worklaod_service = $workloadsService;
        $this->vet_gemi_izin_kontrolu = $vetCanliHIzinKontrol;
        $this->temp_workloads_updater = $telafi_boyunca_temp_workload_guncelleme;
        $this->veteriner_evrak_durumu_kontrolu = $veterinerEvrakDurumularıKontrolu;
    }

    private $workloadCoefficients = [
        'ithalat' => 20,
        'numuneli_ithalat' => 40,
        'transit' => 5,
        'antrepo_giris' => 5,
        'antrepo_varis' => 1,
        'antrepo_varis_dis' => 1,
        'antrepo_sertifika' => 2,
        'antrepo_cikis' => 5,
        'canli_hayvan' => 10,
    ];


    /*
        Girdi olarak evrak türü ve evrak sayısı verilir, çıktı olarak atanması gereken veterinerlerin
        listesinden random bir tanesini verir.

    */

    public function assignVet(string $documentType, $evraks_count)
    {

        /*

        GEÇİCİ OLARAK VETERİNERLERİN ELİNDE BİTMEMİŞ EVRAK OLMA DURUMLARINA GÖRE SEÇİLMELERİ
        KONTROLÜ KALDIRILDI, TEKRAR KONTROL EDİLMELİ...

        - bu kontrolün kaldırılması ile eğer tek seferde çok fazla workload içeren
        bir kayıt yapılırsa veterinerler arasındaki workload farkı çok fazla açılacağı
        için sürekli aynı veterinere evrak atanması gibi bir sorun yaşanabilir.
        */

        $now = now()->setTimezone('Europe/Istanbul'); // tam saat

        // 1. Aktif Veterinerleri Alma
        $veterinerler = $this->aktifVeterinerleriGetir($now);
        if ($veterinerler->isEmpty()) {
            throw new \Exception("Boşta veteriner hekim bulunamadığı için evrak kaydı yapılamamıştır, Lütfen nöbetçi veteriner hekim olduğundan ve müsait olduklarından emin olduktan sonra tekrar deneyiniz!");
        }

        $todayWithHour = now()->setTimezone('Europe/Istanbul'); // tam saat
        $today = $todayWithHour->format('Y-m-d');

        $has_telafi = false;
        $bitmemis_telafiler = [];   // telafisi var ama daha bitirmemiş
        $telafisi_olan_vets = [];   // o gün için telafisi olan veterinerler(telafisini bitirmiş de olabilir bitirmemişde)


        // Elinde işlemde evrak olmayan, işi olmayan veterinerleri bulma - İLK KONTROL
        $isi_olmayan_vets = [];
        foreach ($veterinerler as $vet) {

            // Veteriner canli hayvan gemide mi kontrolü - aslında bu kotrol zaten yapılmıştı , gerek yok gibi
            if ($this->vet_gemi_izin_kontrolu->izin_var_mi($vet->id)) {
                continue;   // izinli ise geç
            }

            // Veterinerin üzerinde çalışmaya devam ettiği evrak var mı kontrolü
            if ($this->veteriner_evrak_durumu_kontrolu->vet_evrak_durum_kontrol($vet->id)) {
                continue;
            }

            // gemi işine gitmemiş ve elinde işi olmayan veterinerleri listeye ekle
            $isi_olmayan_vets[] = $vet;
        }

        /*
            İlk amaç telifisi olan veterinerleri bulup onların telafilerini kapatmak,
            sonrasında normal düzene devam edilecek.
        */

        // İKİNCİ KONTROL
        if (!empty($isi_olmayan_vets)) {    // EĞER MÜSAİT OLAN VETERİNER VAR İSE BUNLAR ARASINDAN SEÇ
            foreach ($isi_olmayan_vets as $vet) {
                $workload = $vet->veterinerinBuYilkiWorkloadi();
                $has_telafi = $workload->telafis()->where('tarih', $today)->exists();
                if ($has_telafi) {

                    $telafisi_olan_vets[] = $vet;

                    /*
                    o gün için veterinerin birden fazla telafisi olabilir,
                    her birinin kalan telafi değeri 0dan büyükse bitmemis_telafiler listesinde topla
                    */
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
        } else {  // EĞER TÜM VETERİNELER DOLU İSE TÜM VETERİNERLER ARASINDA RANDOM SEÇ
            foreach ($veterinerler as $vet) {
                $workload = $vet->veterinerinBuYilkiWorkloadi();
                $has_telafi = $workload->telafis()->where('tarih', $today)->exists();
                if ($has_telafi) {

                    $telafisi_olan_vets[] = $vet;
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
        }





        // Eğer bugün için izinli olmayan veterinerler arasından telafisi olanlar varsa
        // bu veterinerlerin telafileri bitene kadar öncelik bunlara verilecek ,
        // kimsenin telafisi yoksa yada telafilerini bitirmişler ise sistem normal atama işlemini yapar

        // Tüm veterinerlerin telafiisi yokken normal şekilde random bir veterinerin seçilmesi

        if (empty($bitmemis_telafiler)) {


            // 2. Her Veteriner İçin Telafi Hesapla
            /* foreach ($veterinerler as $veteriner) {
            $this->telafiHesapla($veteriner, $now);
            } */

            // 2. En düşük iş yüklü veteriner(ler)i bul
            $min_workload_degeri = PHP_INT_MAX;
            $adayVeterinerler = collect();


            // Her veterinerin bu yıl için aldığı işler karşılaştırılarak en düşük olan(lar) adayVeterinerler arasında eklenir
            foreach ($veterinerler as $vet) {

                // Veterinerler arasından seçerken elinde daha bitmemiş bir evrak olanları geç
                /* if ($this->veteriner_evrak_durumu_kontrolu->vet_evrak_durum_kontrol($vet->id)) {
                    continue;
                } */


                $currentWorkload_degeri = 0; // telafisi olması durumuna göre hangi değerin alınacağına karar verilecek

                // Eğer telafisi olan veterinern varsa bu veterinerlerin  workload
                // modelindeki temp_workload değerkerine bakılarak atama sisteminin çalışması gerekiyor
                if (in_array($vet, $telafisi_olan_vets)) {

                    $currentWorkload_degeri = $vet->veterinerinBuYilkiWorkloadi()->temp_workload;
                } else {  // Eğer veterinerin telafisi yoksa bu sefer normal şekilde workload ının

                    // veterinerinBuYilkiWorkloadi fonksiyonu ile veterinerin bu yıl için bir
                    // workload modeli varsa getirir, yoksa bu yıl için yeni bir tane oluşturur.
                    $currentWorkload_degeri = $vet->veterinerinBuYilkiWorkloadi()->year_workload;
                }



                // Workload değerleri arasından en az olani yada olanları bul
                if ($currentWorkload_degeri < $min_workload_degeri) {
                    $min_workload_degeri = $currentWorkload_degeri;
                    $adayVeterinerler = collect([$vet]);
                } elseif ($currentWorkload_degeri == $min_workload_degeri) {
                    $adayVeterinerler->push($vet);
                }
            }


            // 3. Rastgele bir veterineri seç
            if ($adayVeterinerler->isEmpty()) {
                throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
            }
            $seciliVeteriner = $adayVeterinerler->random();


            // Seçilen veterinerin workload ını veterinerin telafisi olmasına göre farklı şekilde güncellenmesi gerekiyor
            if (in_array($seciliVeteriner, $telafisi_olan_vets)) {

                $this->updateWorkload(
                    $seciliVeteriner,
                    $this->workloadCoefficients[$documentType],
                    true,
                    $evraks_count
                );
            } else {
                $this->updateWorkload(
                    $seciliVeteriner,
                    $this->workloadCoefficients[$documentType],
                    false,
                    $evraks_count
                );
            }

            $this->temp_workloads_updater->all_temp_workloads_update();



            return $seciliVeteriner;
        } else {  // Telafisi olan veya telafisini bitirmemiş veterinerler üzerinde işlem yapma

            // Telafisi olan veterinerler arasında random bir tanesini seçme
            $secilenTelafi = array_rand($bitmemis_telafiler);

            $vet_id = $bitmemis_telafiler[$secilenTelafi]['vet_id'];
            $telafi = $bitmemis_telafiler[$secilenTelafi]['telafi'];

            $seciliVeteriner = User::find($vet_id);
            if (!$seciliVeteriner) {
                throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
            }


            $this->updateWorkload(
                $seciliVeteriner,
                $this->workloadCoefficients[$documentType],
                true,
                $evraks_count
            );
            $telafi->remaining_telafi_workload -= $this->workloadCoefficients[$documentType];
            $telafi->save();


            $this->temp_workloads_updater->all_temp_workloads_update();


            return $seciliVeteriner;
        }
    }



    /*
        İlgili veterinerin evrak iş katsayısı * evrak sayısı kadar bu yılki iş yükü miktarı arttırılır.
        Eğer veterinerin telafisi var ise temp_workload değeri de güncellenir telafisi bitene kadar.
    */
    private function updateWorkload(User $vet, int $coefficient, $has_telafi, $evraks_count)
    {

        // Yapılan evrak sayısına göre workload değerleri çarpılarak güncellenir.

        $today = Carbon::now();

        // Bu fonksiyona gelmeden önce assignVet fonksiyonunda tüm veterinerlere bu yıl için kesin bir tane workload atanmış oluyor.
        $veteriner_bu_yilki_workloadi = $vet->workloads->where('year', $today->year)->first();

        if ($has_telafi) {
            $veteriner_bu_yilki_workloadi->year_workload += $coefficient * $evraks_count;
            $veteriner_bu_yilki_workloadi->temp_workload += $coefficient * $evraks_count;
            $veteriner_bu_yilki_workloadi->total_workload += $coefficient * $evraks_count;
            $veteriner_bu_yilki_workloadi->save();
        } else {
            $veteriner_bu_yilki_workloadi->year_workload += $coefficient * $evraks_count;
            $veteriner_bu_yilki_workloadi->total_workload += $coefficient * $evraks_count;
            $veteriner_bu_yilki_workloadi->save();
        }
    }


    /**
     * Aktif Veterinerleri Getirir
     * - İzinli olmayanlar
     * - Saat 15.30'den sonra sadece nöbetçiler
     */
    private function aktifVeterinerleriGetir(Carbon $simdikiZaman)
    {

        $hata_mesaji = "Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen en az bir aktif veteriner olduğundan emin olduktan sonra tekrar deneyiniz!";

        try {
            $kontrol_zamani = $simdikiZaman->copy()->setTime(15, 30, 0);
            $veterinerler_query = User::role('veteriner')->where('status', 1);

            // İzin (normal ve gemi) kontrolü için temel sorgu parçası
            $izin_kontrol_closure = function ($query) use ($simdikiZaman) {
                $query->where('startDate', '<=', $simdikiZaman)
                    ->where('endDate', '>=', $simdikiZaman);
            };
            $gemi_izin_kontrol_closure = function ($query) use ($simdikiZaman) {
                $query->where('start_date', '<=', $simdikiZaman)
                    ->where('end_date', '>=', $simdikiZaman);
            };

            // 8. Nöbet Kontrolü (15.30 sonrasında sadece nöbetçi olanlar evrak alacak)
            if ($simdikiZaman->greaterThan($kontrol_zamani)) {

                $veterinerler = $veterinerler_query
                    ->whereDoesntHave('izins', $izin_kontrol_closure)
                    ->whereDoesntHave('gemi_izins', $gemi_izin_kontrol_closure)
                    ->whereHas('nobets', function ($sorgu) use ($simdikiZaman) {
                        $sorgu->where('date', $simdikiZaman->format('Y-m-d'));
                    })->get();
            } else {
                // 9. Normal Çalışma Saatleri (Tüm aktif ve izinli olmayanlar evrak alacak)

                $veterinerler = $veterinerler_query
                    ->whereDoesntHave('izins', $izin_kontrol_closure)
                    ->whereDoesntHave('gemi_izins', $gemi_izin_kontrol_closure)
                    ->get();
            }

            // --- YENİ EKLENEN FİLTRELEME MANTIĞI: İŞLEMDE EVRAK KONTROLÜ ---
            // Eğer veterinerin elinde "işlemde" durumunda evrak var ise o veteriner filtreleniyor

            $veterinerler->load(['evraks.evrak.evrak_durumu']);

            $atanabilir_veterinerler = $veterinerler->filter(function ($veteriner) {

                // Veterinerin 'İşlemde' evrağı olup olmadığını kontrol et.
                if ($veteriner->evraks) {
                    $isi_var_mi = $veteriner->evraks->contains(
                        fn($data) =>
                        $data->evrak &&
                            $data->evrak->evrak_durumu &&
                            $data->evrak->evrak_durumu->evrak_durum === 'İşlemde'
                    );

                    // Sadece işi OLMAYANLARı (yani $isi_var_mi false olanları) tut
                    return !$isi_var_mi;
                }

                // Evrağı olmayanlar doğal olarak işi yok sayılır ve tutulur.
                return true;
            });
            // --------------------------------------------------------------------


            if ($atanabilir_veterinerler->isEmpty()) {
                throw new \Exception($hata_mesaji);
            }

            // Atanmaya hazır, "İşlemde" evrağı olmayan veteriner listesini geri dön
            return $atanabilir_veterinerler;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() ?? $hata_mesaji);
        }
    }
}
