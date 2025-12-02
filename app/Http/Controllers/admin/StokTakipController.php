<?php

namespace App\Http\Controllers\admin;

use Error;
use Illuminate\Http\Request;
use App\Models\SaglikSertifika;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StokTakipController extends Controller
{



    public function index()
    {

        /* sorun sağlık sertifikaları listelenirken sıralanamaması, nedeni ise evrak düzenlenirken
         her düzenlemede tüm sağlık sertifikaları silinip baştan kaydedilmesi(sync) , bunu düzeltmek
         için ise hepsini silmeden sadece silinenleri silip yenileri ekleyerek düzeltilecek. */
        $saglik_s = SaglikSertifika::with(
            ['evraks_ithalat', 'evraks_transit', 'evraks_giris', 'evraks_varis', 'evraks_varis_dis', 'evraks_sertifika', 'evraks_canli_hayvan']
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
            } elseif ($sertifika->evraks_varis_dis->isNotEmpty()) {
                $evrak = $sertifika->evraks_varis_dis->first();
                $evrak_type = 'Antrepo Varış(DIŞ)';
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


        //dd($data['saglik_s']);




        return view('admin.stok_takip.index', $data);
    }



    public function ss_edit($ss_id)
    {
        $ss = SaglikSertifika::find($ss_id);
        try {
            if ($ss) {

                $ss = SaglikSertifika::find($ss_id);
                $data['ss'] = $ss;
                return view('admin.stok_takip.edit', $data);
            } else {
                throw new \Exception('İlgili sağlık sertifikası bulunamamdı! - 001');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function ss_edited(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'ssn' => 'required',
            'toplam_miktar' => 'required',
            'kalan_miktar' => 'required',
        ], [
            'ssn.required' => 'Sağlık Sertifika No, alanı eksik!',
            'toplam_miktar.required' => 'Toplam Miktar, alanı eksik!',
            'kalan_miktar.required' => 'Kalan Miktar, alanı eksik!',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik veri girişi! Lütfen Tüm Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error', $errors);
        }

        try {

            $ss = SaglikSertifika::find($request->ss_id);

            $ss->ssn = $request->ssn;
            $ss->toplam_miktar = $request->toplam_miktar;
            $ss->kalan_miktar = $request->kalan_miktar;

            $ss->save();

            return redirect()->route('admin.stok_takip.index')->with('success', 'Sağlık sertifikası başarıyla düzenlenmiştir.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
