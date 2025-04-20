<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SaglikSertifika;
use Illuminate\Http\Request;

class StokTakipController extends Controller
{



    public function index()
    {

         /* sorun sağlık sertifikaları listelenirken sıralanamaması, nedeni ise evrak düzenlenirken
         her düzenlemede tüm sağlık sertifikaları silinip baştan kaydedilmesi(sync) , bunu düzeltmek
         için ise hepsini silmeden sadece silinenleri silip yenileri ekleyerek düzeltilecek. */
        $saglik_s = SaglikSertifika::with(
            ['evraks_ithalat', 'evraks_transit', 'evraks_giris', 'evraks_varis', 'evraks_sertifika','evraks_canli_hayvan']
        )->orderBy('created_at', 'desc')->get(); // Veritabanında sıralama yapılmadı, map ile sıralanacak

        // Sağlık sertifikalarının bağlı olduğu tek evrağı bulma ve sıralama işlemi
        $saglik_s = $saglik_s->map(function ($sertifika) {
            $evrak = null;
            $evrak_type = null;

            // Evrağı sırasıyla kontrol etme (önce evrak türleri, sonra sıralama)
            if ($sertifika->evraks_ithalat->isNotEmpty()) {
                $evrak = $sertifika->evraks_ithalat->first();
                $evrak_type = 'İthalat';
            } elseif ($sertifika->evraks_transit->isNotEmpty()) {
                $evrak = $sertifika->evraks_transit->first();
                $evrak_type = 'Transit';
            } elseif ($sertifika->evraks_giris->isNotEmpty()) {
                $evrak = $sertifika->evraks_giris->first();
                $evrak_type = 'Antrepo Giriş';
            } elseif ($sertifika->evraks_varis->isNotEmpty()) {
                $evrak = $sertifika->evraks_varis->first();
                $evrak_type = 'Antrepo Varış';
            } elseif ($sertifika->evraks_sertifika->isNotEmpty()) {
                $evrak = $sertifika->evraks_sertifika->first();
                $evrak_type = 'Antrepo Sertifika';
            } elseif ($sertifika->evraks_canli_hayvan->isNotEmpty()) {
                $evrak = $sertifika->evraks_canli_hayvan->first();
                $evrak_type = 'Canlı Hayvan';
            }

            // Her sertifikaya ait evrak türü ile birlikte döndürülen veri
            return [
                'saglik_sertifika' => $sertifika,
                'evrak' => $evrak,
                'evrak_type' => $evrak_type,
                'evrak_morph_class' => $evrak->getMorphClass(),
            ];
        });

        // Sağlık sertifikalarını `created_at` alanına göre azalan sırayla sıralama
        $data['saglik_s'] = $saglik_s;




        return view('admin.stok_takip.index', $data);
    }
}
