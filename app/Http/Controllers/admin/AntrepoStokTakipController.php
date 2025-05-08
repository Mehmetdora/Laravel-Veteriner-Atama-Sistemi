<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\SaglikSertifika;
use App\Http\Controllers\Controller;
use App\Models\GirisAntrepo;

class AntrepoStokTakipController extends Controller
{
    public function index()
    {
        /*
        Bu sayfada sadece antrepolar listelenecek
        */

        $antrepos = GirisAntrepo::all();

        $antrepos = $antrepos->map(function ($antrepo) {
            $sertifika_count = 0;

            if ($antrepo->evraks_antrepo_giris->isNotEmpty()) {
                $evraks = $antrepo->evraks_antrepo_giris;
                foreach ($evraks as $evrak) {
                    $sertifika_count += count($evrak->saglikSertifikalari);
                }
            }

            return [
                'antrepo' => $antrepo,
                'sertifika_count' => $sertifika_count,
            ];
        });

        $data['antrepos'] = $antrepos;



        return view('admin.antrepo_stok_takip.index', $data);
    }


    public function antrepo_detail($id)
    {

        $antrepo = GirisAntrepo::find($id);
        $sertifikas = [];

        // seçilen antrepoya ait tüm sağlık sertifikarllını al
        if ($antrepo->evraks_antrepo_giris->isNotEmpty()) {
            $evraks = $antrepo->evraks_antrepo_giris;
            foreach ($evraks as $evrak) {

                // Evrağa ait tüm sertifikalarını al
                if ($evrak->saglikSertifikalari->isNotEmpty()) {
                    foreach ($evrak->saglikSertifikalari as $sertifika) {
                        $sertifikas[] = $sertifika;
                    }
                }
            }
        }

        $collection_s = collect($sertifikas);

        // Sağlık sertifikalarının bağlı olduğu tek evrağı bulma ve sıralama işlemi
        $collection_s = $collection_s->map(function ($sertifika) use($antrepo) {
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
        $data['kayitlar'] = $collection_s;
        $data['antrepo'] = $antrepo;

        return view('admin.antrepo_stok_takip.antrepo_detail', $data);

    }
}
