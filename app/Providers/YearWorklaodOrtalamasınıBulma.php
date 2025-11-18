<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Telafi;
use Error;

use function Laravel\Prompts\error;

class YearWorklaodOrtalamasınıBulma
{


    protected $veteriner_durum_kontrol;

    function __construct(VeterinerDurumKontrolleri $veterinerDurumKontrolleri)
    {
        $this->veteriner_durum_kontrol = $veterinerDurumKontrolleri;
    }



    /**
     * Burada telafisi olmayan veterinerlerin yıllık ortalama workload değerinin
     * hesaplanmasının nedeni, izindeki veterinerlerin her izin günü için otomatik telafisi olan veterinerlerin
     * temp_workload değerleri güncellenirken bu güncellenecek yeni değerini telafisi olmayan
     * veterinerin year_workload değerlerinin ortalaması ile güncellenir.
     *
     * Buradaki sorun ise yine izindeki veterinerlerin temp_workload değerlerini güncellemek
     * için telafisi olmayana veterinerlerin year_workload değerinin kullanacakken eğer bu telafisi
     * olmayan hiç veteriner yok ise temp_workload değerini güncelleyecek bir sayı da olmuyor.
     *
     *

     */
    public function DigerVetsOrtalamaYearWorklaodDegeri()
    {



        /*

        Veterinerler arasında bu gün için telafisi ve izini olmayanlar seçilir, bu veterinerlerin
        year_workload değerlerinin ortalaması alınarak dönülüyor.

        */

        /**
         * Bu fonksiyon sadece yeni bir izin oluşturulurken temp_workload değerinin güncellenmesi sırasında ,
         * birde eğer izinde olan bir veteriner varsa bu veterinerin temp_workload değerinin izinden döndüğünde
         * diğerleri ile arasında workload farkı olmaması için kullanılıyor. Eğer bu ortalama year_workload değerinin
         * hesaplanabilmesi için hiç uygun(telafisi olmayan) veteriner yoksa aktif ve telafisi olan veterinerlerin
         * ortalama temp_workload değeri kullanılabilir.
         */


        /**
         * Buradaki sorun izinde olan veterinerlerin temp_workload değerlerinin güncellenmesi için gerekli olan
         * ortalama yıllık workload değerini hesaplarken eğer hesaplama sırasında hiç aktif ve telafisi olmayan
         * veteriner yoksa ortalama hesaplanması sırasında 0 ' a bölünme hatası alınması.
         */



        $simdikiZaman = now()->setTimezone('Europe/Istanbul'); // tam saat
        $vets = $this->veteriner_durum_kontrol->aktifTelafisiOlmayanaVeterinerleriGetir($simdikiZaman);

        // Eğer hiç aktif-telafisi olmayan veteriner yoksa , telafisi olanların ortalaması kullanılacak
        if (count($vets) == 0) {
            $vets = $this->veteriner_durum_kontrol->aktifTelafisiOlanVeterinerleriGetir($simdikiZaman);
        }


        if (count($vets) == 0) {
            throw new \Exception("Boşta veteriner hekim bulunamadığı için evrak kaydı sırasında hata oluştu, Lütfen en az bir veteriner hekim olduğundan ve müsait olduklarından emin olduktan sonra tekrar deneyiniz! Hata Kodu: 005");
        }

        $toplam_year_workload = 0;
        $toplam_vets_count = count($vets);
        $ortalama_year_workload = 0;

        foreach ($vets as $vet) {
            $workload = $vet->veterinerinBuYilkiWorkloadi();
            $toplam_year_workload += $workload->year_workload;
        }

        $ortalama_year_workload = (int)($toplam_year_workload / $toplam_vets_count);



        return $ortalama_year_workload;
    }
}
