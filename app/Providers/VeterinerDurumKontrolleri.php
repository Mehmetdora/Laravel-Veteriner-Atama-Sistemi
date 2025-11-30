<?php

namespace App\Providers;

use App\Models\GemiIzni;
use App\Models\User;
use App\Models\Telafi;
use Carbon\Carbon;

class VeterinerDurumKontrolleri
{

    /**
     * Aktif Veterinerleri Getirir
     * - İzinli olmayanlar(gemi ve normal izin),
     * - Saat 15.30'den sonra ise sadece nöbetçiler, önce nöbetçi olmayanlar,
     *
     */
    public function aktifVeterinerleriGetir(Carbon $simdikiZaman)
    {

        $hata_mesaji = "Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen en az bir aktif veteriner olduğundan emin olduktan sonra tekrar deneyiniz! , Hata Kodu: 006";

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
                // ek olarak artık akşam nöbetçi olanlar gün içinde evrak alamayacak.

                $veterinerler = $veterinerler_query
                    ->whereDoesntHave('izins', $izin_kontrol_closure)
                    ->whereDoesntHave('gemi_izins', $gemi_izin_kontrol_closure)
                    ->whereDoesntHave('nobets', function ($sorgu) use ($simdikiZaman) {
                        $sorgu->where('date', $simdikiZaman->format('Y-m-d'));
                    })
                    ->get();
            }
            // Yeni Özellik
            /**
             * Bundan sonra veterinerler seçilirken önce normla kontroller yapılacak,
             * eğer veterinerlerin elinde "işlemde" evrağı yoksa bu şekilde normal akışta seçilecekler,
             * ama eğer hiçbir veteriner boşta değilse;
             * - tüm veterinerlerin elindeki "işlemde" türündeki evraklarının toplam workload değerleri karşılaştırılır,
             * - en az işlemde workload ı olan/olanlar arasından random birine evrağı ata
             */

            $veteriner_islemde_workload = [];
            $veterinerler->load(['evraks.evrak.evrak_durumu']);

            $islenmis_veri = $veterinerler->map(function ($veteriner) {
                $islemde_mi = $veteriner->evraks->contains(
                    fn($data) =>
                    $data->evrak &&
                        $data->evrak->evrak_durumu &&
                        $data->evrak->evrak_durumu->evrak_durum === 'İşlemde'
                );

                $evrak_workload = $veteriner->evraks
                    ->filter(fn($d) => $d->evrak?->evrak_durumu?->evrak_durum === 'İşlemde')
                    ->sum(fn($data) => $data->evrak->difficulty_coefficient ?? 0);

                return [
                    'veteriner' => $veteriner,
                    'musait_mi' => !$islemde_mi,
                    'evrak_workload'  => $evrak_workload
                ];
            });

            $veteriner_islemde_workload = $islenmis_veri->map(fn($item) => [
                'vet' => $item['veteriner'],
                'islemde_workload' => $item['evrak_workload']
            ]);


            // toplanan veteriner bilgileri içinden normal şekilde veterinerlerin filtrelenmesi
            $atanabilir_veterinerler = $islenmis_veri
                ->where('musait_mi', true)
                ->pluck('veteriner');

            // Hata vermek yerine , elinde iş de olsa veterinerin birine bu evrak atanacak
            // bu nedenle elinde işlemde evrağı olanlar arasından seçerek , veteriner listesini dön


            if ($atanabilir_veterinerler->isEmpty()) {
                //throw new \Exception($hata_mesaji);

                $min_value = PHP_INT_MAX;   // başlangıç değeri olarak başlat
                $adayVeterinerler = collect();

                foreach ($veteriner_islemde_workload as $vet_collection) {
                    $currentWorkload_degeri = $vet_collection['islemde_workload']; // telafisi olması durumuna göre hangi değerin alınacağına karar verilecek

                    // Workload değerleri arasından en az olani yada olanları bul
                    if ($currentWorkload_degeri < $min_value) {
                        $min_value = $currentWorkload_degeri;
                        $adayVeterinerler = collect([$vet_collection['vet']]);
                    } elseif ($currentWorkload_degeri == $min_value) {
                        $adayVeterinerler->push($vet_collection['vet']);
                    }
                }

                return $adayVeterinerler;
            }

            return $atanabilir_veterinerler;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() ?? $hata_mesaji);
        }
    }



    /**
     * Aktif Veterinerleri Getirir
     * - İzinli olmayan(gemi ve normal izin),
     * - Nöbetçi olan,
     * - Elinde "işlemde" durumunda evrağı olmayanları
     */

    public function aktifNobetciVeterinerleriGetir(Carbon $simdikiZaman)
    {

        $hata_mesaji = "Nöbetçi veteriner hekimlerinin seçilmesi sırasında bir hata oluştu, lütfen nöbetçi veterinerlerin olduğundan emin olduktan sonra tekrar deneyiniz! , Hata Kodu: 007";

        try {
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

            $veterinerler = $veterinerler_query
                ->whereDoesntHave('izins', $izin_kontrol_closure)
                ->whereDoesntHave('gemi_izins', $gemi_izin_kontrol_closure)
                ->whereHas('nobets', function ($sorgu) use ($simdikiZaman) {
                    $sorgu->where('date', $simdikiZaman->format('Y-m-d'));
                })->get();



            // --- FİLTRELEME : İŞLEMDE EVRAK KONTROLÜ ---
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

            // Atanmaya hazır, izinde olmayan, nöbetçi olan, "İşlemde" evrağı olmayan veteriner listesini geri dön
            return $atanabilir_veterinerler;
        } catch (\Exception $e) {
            throw new \Exception($hata_mesaji);
        }
    }


    /**
     * Aktif Veterinerleri Getirir
     * - İzinli olmayan(gemi ve normal izin),
     * - Telafisi olmayan
     */

    public function aktifTelafisiOlmayanaVeterinerleriGetir(Carbon $simdikiZaman)
    {
        $hata_mesaji = "Aktif veteriner hekimlerinin seçilmesi sırasında bir hata oluştu, Hata Kodu: 008";

        try {

            $today_yıl_ay_gun = $simdikiZaman->format('Y-m-d');
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

            $veterinerler = $veterinerler_query
                ->whereDoesntHave('izins', $izin_kontrol_closure)
                ->whereDoesntHave('gemi_izins', $gemi_izin_kontrol_closure)
                ->whereDoesntHave('workloads', function ($sorgu) use ($today_yıl_ay_gun) {
                    $sorgu->whereHas('telafis', function ($alt_sorgu) use ($today_yıl_ay_gun) {
                        $alt_sorgu->where('tarih', $today_yıl_ay_gun);
                    });
                })->get();

            return $veterinerler;
        } catch (\Exception $e) {
            throw new \Exception($hata_mesaji);
        }
    }


    /**
     * Aktif Veterinerleri Getirir
     * - İzinli olmayan(gemi ve normal izin),
     * - Telafisi olan
     */
    public function aktifTelafisiOlanVeterinerleriGetir(Carbon $simdikiZaman)
    {
        $hata_mesaji = "Aktif veteriner hekimlerinin seçilmesi sırasında bir hata oluştu, Hata Kodu: 009";

        try {

            $today_yıl_ay_gun = $simdikiZaman->format('Y-m-d');
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

            $veterinerler = $veterinerler_query
                ->whereDoesntHave('izins', $izin_kontrol_closure)
                ->whereDoesntHave('gemi_izins', $gemi_izin_kontrol_closure)
                ->whereHas('workloads', function ($sorgu) use ($today_yıl_ay_gun) {
                    $sorgu->whereHas('telafis', function ($alt_sorgu) use ($today_yıl_ay_gun) {
                        $alt_sorgu->where('tarih', $today_yıl_ay_gun);
                    });
                })->get();

            return $veterinerler;
        } catch (\Exception $e) {
            throw new \Exception($hata_mesaji . " - " . $e);
        }
    }
}
