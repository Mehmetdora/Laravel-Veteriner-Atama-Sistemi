<?php

namespace App\Http\Controllers\memur;

use Carbon\Carbon;
use App\Models\Urun;
use App\Models\User;
use App\Models\UsksNo;
use App\Models\UserEvrak;
use App\Models\EvrakDurum;
use App\Models\AracPlakaKg;
use App\Models\EvrakIthalat;
use App\Models\EvrakTransit;
use App\Models\GirisAntrepo;
use Illuminate\Http\Request;
use App\Models\SaglikSertifika;
use App\Providers\AtamaServisi;
use App\Models\EvrakCanliHayvan;
use App\Models\EvrakAntrepoCikis;
use App\Models\EvrakAntrepoGiris;
use App\Models\EvrakAntrepoVaris;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\EvrakCanliHayvanGemi;
use App\Models\EvrakAntrepoSertifika;
use App\Models\EvrakAntrepoVarisDis;
use Illuminate\Support\Facades\Validator;
use App\Providers\CanliHGemiIzniOlusturma;
use App\Providers\YeniYilWorkloadsGuncelleme;
use App\Providers\VeterinerEvrakDurumularıKontrolu;
use App\Providers\OrtalamaGunlukWorkloadDegeriBulma;
use App\Providers\DailyTotalWorkloadUpdateORCreateService;
use App\Providers\SsnKullanarakAntrepo_GVeterineriniBulma;
use App\Providers\WorkloadsService;

class EvrakController extends Controller
{

    protected $ssn_ile_antrepo_giris_vet_bulma_servisi;
    protected $veteriner_evrak_durum_kontrol_servisi;
    protected $daily_total_worklaod_update_create_servisi;
    protected $ortalama_gunluk_workload_degeri_bulma;
    protected $atamaServisi;
    protected $yeni_yil_workloads_guncelleme;
    protected $atanacak_veteriner;
    protected $gemi_izni_olusturma;
    protected $workloads_service;


    function __construct(WorkloadsService $workloadsService, CanliHGemiIzniOlusturma $canliHGemiIzniOlusturma, YeniYilWorkloadsGuncelleme $yeni_yil_workloads_guncelleme, AtamaServisi $atamaServisi, OrtalamaGunlukWorkloadDegeriBulma $ortalama_gunluk_workload_degeri_bulma, DailyTotalWorkloadUpdateORCreateService $daily_total_workload_update_orcreate_service, VeterinerEvrakDurumularıKontrolu $veterinerEvrakDurumularıKontrolu, SsnKullanarakAntrepo_GVeterineriniBulma $ssn_kullanarak_antrepo_gveterinerini_bulma)
    {
        $this->workloads_service = $workloadsService;
        $this->gemi_izni_olusturma = $canliHGemiIzniOlusturma;
        $this->yeni_yil_workloads_guncelleme = $yeni_yil_workloads_guncelleme;
        $this->ortalama_gunluk_workload_degeri_bulma = $ortalama_gunluk_workload_degeri_bulma;
        $this->daily_total_worklaod_update_create_servisi = $daily_total_workload_update_orcreate_service;
        $this->veteriner_evrak_durum_kontrol_servisi = $veterinerEvrakDurumularıKontrolu;
        $this->ssn_ile_antrepo_giris_vet_bulma_servisi = $ssn_kullanarak_antrepo_gveterinerini_bulma;
        $this->atamaServisi = $atamaServisi;
    }


    public function index()
    {

        $evraks_all = collect()
            ->merge(EvrakIthalat::with(['veteriner.user', 'urun', 'evrak_durumu'])->get())
            ->merge(EvrakTransit::with(['veteriner.user', 'urun', 'evrak_durumu'])->get())
            ->merge(EvrakCanliHayvan::with(['veteriner.user', 'urun', 'evrak_durumu'])->get())
            ->merge(EvrakAntrepoGiris::with(['veteriner.user', 'urun', 'evrak_durumu'])->get())
            ->merge(EvrakAntrepoVaris::with(['veteriner.user',  'evrak_durumu'])->get())
            ->merge(EvrakAntrepoVarisDis::with(['veteriner.user',  'evrak_durumu'])->get())
            ->merge(EvrakAntrepoSertifika::with(['veteriner.user', 'usks', 'urun', 'evrak_durumu'])->get())
            ->merge(EvrakAntrepoCikis::with(['veteriner.user', 'urun', 'evrak_durumu'])->get())
            ->merge(EvrakCanliHayvanGemi::with(['veteriner.user', 'evrak_durumu'])->get());

        // `created_at`'e göre azalan sırayla sıralama
        $evraks_all = $evraks_all->sortByDesc('created_at');
        $data['evraks_all'] = $evraks_all;


        return view('memur.evrak_kayit.index', $data);
    }

    public function detail($type, $evrak_id)
    {

        $type = explode("\\", $type);
        $type = end($type);

        if ($type == "EvrakIthalat") {
            $data['evrak'] = EvrakIthalat::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakTransit") {
            $data['evrak'] = EvrakTransit::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoGiris") {
            $data['evrak'] = EvrakAntrepoGiris::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoVaris") {
            $data['evrak'] = EvrakAntrepoVaris::with(['veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoVarisDis") {
            $data['evrak'] = EvrakAntrepoVarisDis::with(['veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoSertifika") {
            $data['evrak'] = EvrakAntrepoSertifika::with(['urun', 'veteriner.user',  'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoCikis") {
            $data['evrak'] = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakCanliHayvan") {
            $data['evrak'] = EvrakCanliHayvan::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakCanliHayvanGemi") {
            $data['evrak'] = EvrakCanliHayvanGemi::with(['veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        }

        $data['type'] = $type;


        return view('memur.evrak_kayit.detail', $data);
    }


    public function create()
    {

        $today = now()->setTimezone('Europe/Istanbul'); // tam saat
        $yil = $today->year; // Değişecek kısım
        $ornek_usks = sprintf('33VSKN01.USKS.%d-', $yil); // sondaki numarayı 1 arttırma

        $data['ornek_usks'] = $ornek_usks;
        $data['uruns'] = Urun::all();
        $data['giris_antrepos'] = GirisAntrepo::actives();
        $data['veteriners'] = User::role('veteriner')->where('status', 1)->get();
        return view('memur.evrak_kayit.create', $data);
    }

    public function created(Request $request)
    {


        // $formData[0]['evrak_turu'] değeri gelen evraklarını türünü sayısal olarak verir.
        // 0-> ithalat
        // 1-> transit
        // 2-> Atrepo giriş
        // 3-> Atrepo varış
        // 4-> Atrepo sertifika
        // 5-> Atrepo çıkış
        // 6-> Canlı Hayvan

        /*
        'ithalat' => 20,
        'transit' => 5,
        'antrepo_giris' => 5,
        'antrepo_varis' => 1,
        'antrepo_sertifika' => 2,
        'antrepo_cikis' => 5,
        'canli_hayvan' => 10,
        */


        //Evrak Kaydından önce yeni yıl kontrolü yapılarak workloadları duruma göre güncelleme
        $this->yeni_yil_workloads_guncelleme->YeniYilWorkloadsGuncelleme();



        $today = now()->setTimezone('Europe/Istanbul'); // tam saat

        $formData = json_decode($request->formData, true); // JSON stringi diziye çeviriyoruz


        if (!$formData) {
            return redirect()->back()->with('error', 'Geçersiz veri formatı!');
        }



        // İlk gelen formdaki evrağın türü ne ise diğerleride aynı türde olduğunu
        // varsayarak evrak türünü belirleyip tüm evrakları for ile özel validate işlemi uygulandı
        $errors = [];

        // Validations
        if ($formData[0]['evrak_turu'] == 0) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required|unique:evrak_ithalats,evrakKayitNo',
                    'vgbOnBildirimNo' => 'required',
                    'ss_no' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => [
                        'required',
                        'max:9999999.999'
                    ],
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'arac_plaka_kg' => 'required',
                    'arac_plaka_kg.*.miktar' => ['required', 'numeric', 'max:9999999,999'],
                    'girisGumruk' => 'required',
                ], [
                    'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                    'siraNo.unique' => 'Evrak Kayıt No, alanı benzersiz olmalı !',
                    'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                    'ss_no.required' => 'Sağlık Sertifikası, alanı eksik!',
                    'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                    'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                    'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                    'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                    'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                    'urunKG.max' => 'Ürünün toplam miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol edinizi!',
                    'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                    'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                    'arac_plaka_kg.required' => 'Araç Plakası ve Yük Miktarı(KG), alanı eksik!',
                    'arac_plaka_kg.*.miktar.max' => 'Araç Plakası Yük Miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol ediniz!',
                    'girisGumruk.required' => 'Giriş Gümrüğü, alanı eksik!',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 1) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required|unique:evrak_transits,evrakKayitNo',
                    'vgbOnBildirimNo' => 'required',
                    'ss_no' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => [
                        'required',
                        'numeric',
                        'max:9999999.999'
                    ],
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'aracPlaka' => 'required',
                    'girisGumruk' => 'required',
                    'cıkısGumruk' => 'required',
                ], [
                    'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                    'siraNo.unique' => 'Evrak Kayıt No, alanı benzersiz olmalı !',
                    'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                    'ss_no.required' => 'Sağlık Sertifikası, alanı eksik!',
                    'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                    'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                    'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                    'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                    'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                    'urunKG.max' => 'Ürünün toplam miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol edinizi!',
                    'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                    'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                    'aracPlaka.required' => 'Araç Plakası & Konteyner No, alanı eksik!',
                    'girisGumruk.required' => 'Giriş Gümrüğü, alanı eksik!',
                    'cıkısGumruk.required' => 'Çıkış Gümrüğü, alanı eksik!',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 2) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required|unique:evrak_antrepo_giris,evrakKayitNo',
                    'vgbOnBildirimNo' => 'required',
                    'ss_no' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => [
                        'required',
                        'numeric',
                        'max:9999999.999'
                    ],
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'aracPlaka' => 'required',
                    'girisGumruk' => 'required',
                    'giris_antrepo_id' => 'required',
                ], [
                    'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                    'siraNo.unique' => 'Evrak Kayıt No, alanı benzersiz olmalı !',
                    'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                    'ss_no.required' => 'Sağlık Sertifikası, alanı eksik!',
                    'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                    'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                    'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                    'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                    'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                    'urunKG.max' => 'Ürünün toplam miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol edinizi!',
                    'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                    'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                    'aracPlaka.required' => 'Araç Plakası veya Konteyner No, alanı eksik!',
                    'girisGumruk.required' => 'Giriş Gümrüğü, alanı eksik!',
                    'giris_antrepo_id.required' => 'Varış Antrepo, alanı eksik!',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 3) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required|unique:evrak_antrepo_varis,evrakKayitNo',
                    'oncekiVGBOnBildirimNo' => 'required',
                    'vetSaglikSertifikasiNo' => 'required',
                    'vetSaglikSertifikasiNo.*.miktar' => ['required', 'numeric', 'max:9999999,999'],
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => [
                        'required',
                        'numeric',
                        'max:9999999.999'
                    ],
                    'urunlerinBulunduguAntrepo' => 'required',
                ], [
                    'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                    'siraNo.unique' => 'Evrak Kayıt No, alanı benzersiz olmalı !',
                    'oncekiVGBOnBildirimNo.required' => 'Önceki VGB Numarası, alanı eksik!',
                    'vetSaglikSertifikasiNo.required' => 'Sağlık Sertifikası, alanı eksik!',
                    'vetSaglikSertifikasiNo.*.miktar.max' => 'Sağlık Setifikalarının miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol ediniz!',
                    'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                    'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                    'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                    'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                    'urunKG.max' => 'Ürünün toplam miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol edinizi!',
                    'urunlerinBulunduguAntrepo.required' => 'Giriş Antrepo, alanı eksik!',

                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 4) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required|unique:evrak_antrepo_sertifikas,evrakKayitNo',
                    'vgbNo' => 'required',
                    'vetSaglikSertifikasiNo' => 'required',
                    'vetSaglikSertifikasiNo.*.miktar' => ['required', 'numeric', 'max:9999999,999'],
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => [
                        'required',
                        'numeric',
                        'max:9999999.999'
                    ],
                    'orjinUlke' => 'required',
                    'aracPlaka' => 'required',
                    'cikis_antrepo' => 'required',
                ], [
                    'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                    'siraNo.unique' => 'Evrak Kayıt No, alanı benzersiz olmalı !',
                    'vgbNo.required' => 'Antrepo giriş VGB numarası, alanı eksik!',
                    'vetSaglikSertifikasiNo.required' => 'Sağlık Sertifikası, alanı eksik!',
                    'vetSaglikSertifikasiNo.*.miktar.max' => 'Sağlık Setifikalarının miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol ediniz!',
                    'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                    'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                    'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                    'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                    'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                    'urunKG.max' => 'Ürünün toplam miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol edinizi!',
                    'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                    'aracPlaka.required' => 'Araç Plakası veya Konteyner No, alanı eksik!',
                    'cikis_antrepo.required' => 'Çıkış Antreposu, alanı eksik!',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 5) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required|unique:evrak_antrepo_cikis,evrakKayitNo',
                    'vgbOnBildirimNo' => 'required',
                    'usks_no' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => [
                        'required',
                        'numeric',
                        'max:9999999.999'
                    ],
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'aracPlaka' => 'required',
                    'cıkısGumruk' => 'required',
                ], [
                    'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                    'siraNo.unique' => 'Evrak Kayıt No, alanı benzersiz olmalı !',
                    'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                    'usks_no.required' => 'USKS Numarası, alanı eksik!',
                    'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                    'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                    'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                    'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                    'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                    'urunKG.max' => 'Ürünün toplam miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol edinizi!',
                    'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                    'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                    'aracPlaka.required' => 'Araç Plakası veya Konteyner No, alanı eksik!',
                    'cıkısGumruk.required' => 'Çıkış Gümrüğü, alanı eksik!',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 6) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required|unique:evrak_canli_hayvans,evrakKayitNo',
                    'vgbOnBildirimNo' => 'required',
                    'vetSaglikSertifikasiNo' => 'required',
                    'vetSaglikSertifikasiNo.*.miktar' => ['required', 'numeric', 'max:9999999,999'],
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'hayvanSayisi' => 'required',
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'girisGumruk' => 'required',
                    'cıkısGumruk' => 'required',
                ], [
                    'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                    'siraNo.unique' => 'Evrak Kayıt No, alanı benzersiz olmalı !',
                    'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                    'vetSaglikSertifikasiNo.required' => 'Sağlık Sertifikası, alanı eksik!',
                    'vetSaglikSertifikasiNo.*.miktar.max' => 'Sağlık Setifikalarının miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol ediniz!',
                    'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                    'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                    'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                    'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                    'hayvanSayisi.required' => 'Başvuru Yapılan Hayvan Sayısı(Baş Sayısı), alanı eksik!',
                    'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                    'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                    'girisGumruk.required' => 'Giriş Gümrüğü, alanı eksik!',
                    'cıkısGumruk.required' => 'Çıkış Gümrüğü, alanı eksik!',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 7) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'hayvan_sayisi' => 'required',
                    'veteriner_id' => 'required',
                    'start_date' => 'required',
                    'day_count' => 'required',
                ], [
                    'hayvan_sayisi' => 'Hayvan Sayısı, alanı eksik!',
                    'veteriner_id' => 'Veteriner Hekim, alanı eksik!',
                    'start_date' => 'Başlangıç Tarihi, alanı eksik!',
                    'day_count' => 'Kaç Günlük, alanı eksik!',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 8) {    // Antrepo Varış(DIŞ)
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required|unique:evrak_antrepo_varis_dis,evrakKayitNo',
                    'oncekiVGBOnBildirimNo' => 'required',
                    'vetSaglikSertifikasiNo' => 'required',
                    'vetSaglikSertifikasiNo.*.miktar' => ['required', 'numeric', 'max:9999999,999'],
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => [
                        'required',
                        'numeric',
                        'max:9999999.999'
                    ],
                    'urunlerinBulunduguAntrepo' => 'required',
                ], [
                    'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                    'siraNo.unique' => 'Evrak Kayıt No, alanı benzersiz olmalı !',
                    'oncekiVGBOnBildirimNo.required' => 'Önceki VGB Numarası, alanı eksik!',
                    'vetSaglikSertifikasiNo.required' => 'Sağlık Sertifikası, alanı eksik!',
                    'vetSaglikSertifikasiNo.*.miktar.max' => 'Sağlık Setifikalarının miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol ediniz!',
                    'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                    'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                    'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                    'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                    'urunKG.max' => 'Ürünün toplam miktarı 9.999.999,999 KG ı geçemez, lütfen değerleri kontrol edinizi!',
                    'urunlerinBulunduguAntrepo.required' => 'Giriş Antrepo, alanı eksik!',

                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        }

        // Eğer hata varsa, geriye yönlendir ve tüm hataları göster
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->with('error', $errors);
        }


        //EĞER TEK SEFERDE GELEN EVRAK SAYISI 1 DEN FAZLA İSE TÜM EVRAKLARI LİMİTE GÖRE BAKIP TEK BİR VETERİNERE ATANMASI GEREKİYOR.
        $gelen_evrak_sayisi = count($formData) - 1;


        // 0-> ithalat
        // 1-> transit
        // 2-> Atrepo giriş
        // 3-> Atrepo varış
        // 4-> Atrepo sertifika
        // 5-> Atrepo çıkış
        // 6-> Canlı Hayvan

        try {
            switch ($formData[0]['evrak_turu']) {
                case 0:
                    $this->atanacak_veteriner = $this->atamaServisi->assignVet('ithalat', $gelen_evrak_sayisi);
                    break;
                case 1:
                    $this->atanacak_veteriner = $this->atamaServisi->assignVet('transit', $gelen_evrak_sayisi);
                    break;
                case 2:
                    $this->atanacak_veteriner = $this->atamaServisi->assignVet('antrepo_giris', $gelen_evrak_sayisi);
                    break;
                case 3:
                    $this->atanacak_veteriner = $this->atamaServisi->assignVet('antrepo_varis', $gelen_evrak_sayisi);
                    break;
                case 4:
                    $this->atanacak_veteriner = $this->atamaServisi->assignVet('antrepo_sertifika', $gelen_evrak_sayisi);
                    break;
                case 5:
                    $this->atanacak_veteriner = $this->atamaServisi->assignVet('antrepo_cikis', $gelen_evrak_sayisi);
                    break;
                case 6:
                    $this->atanacak_veteriner = $this->atamaServisi->assignVet('canli_hayvan', $gelen_evrak_sayisi);
                    break;
                case 7;
                    // Veteriner seçmeye gerek yok
                    break;
                case 8;     //Evrak antrepo Varış(DIŞ)
                    $this->atanacak_veteriner = $this->atamaServisi->assignVet('antrepo_varis_dis', $gelen_evrak_sayisi);
                    break;

                default:
                    return redirect()->back()->withErrors($errors)->with('error', 'Hatalı evrak türü seçiminden dolayı evrak oluşturulamamıştır, Lütfen tekrar deneyiniz!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }



        // Veritabanı başlangıç durumu
        DB::beginTransaction();

        try {
            $saved_count = 0; // Başarıyla kaydedilen evrak sayısı
            $today = Carbon::now();

            if ($formData[0]['evrak_turu'] == 0) {
                for ($i = 1; $i < count($formData); $i++) {

                    $yeni_evrak = new EvrakIthalat;

                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->vgbOnBildirimNo = $formData[$i]["vgbOnBildirimNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]['urunKG'];
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    foreach ($formData[$i]["arac_plaka_kg"] as $plaka_kg) {
                        $new_arac_plaka_kg = new AracPlakaKg;
                        $new_arac_plaka_kg->miktar = $plaka_kg["miktar"];
                        $new_arac_plaka_kg->arac_plaka = $plaka_kg["plaka"];
                        $new_arac_plaka_kg->evrak_ithalat_id = $yeni_evrak->id;
                        $new_arac_plaka_kg->save();
                    }

                    // Veterineri sistem limite göre atayacak
                    $veteriner = $this->atanacak_veteriner;


                    if (!$urun) {
                        throw new \Exception("Gerekli ilişkili ürün verileri hatalı yada eksik olduğu için evrak kaydı yapılamamıştır, Lütfen gerekli bilgileri doğru bir şekilde doldurup tekrar deneyiniz!");
                    } elseif (!$veteriner) {
                        throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
                    }

                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);
                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikasını kaydetme
                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $formData[$i]['ss_no'];
                    $saglik_sertfika->toplam_miktar = $formData[$i]['urunKG'];
                    $saglik_sertfika->kalan_miktar = $formData[$i]['urunKG'];
                    $saglik_sertfika->save();
                    $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);



                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('ithalat');

                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 1) {
                for ($i = 1; $i < count($formData); $i++) {
                    $yeni_evrak = new EvrakTransit;

                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->vgbOnBildirimNo = $formData[$i]["vgbOnBildirimNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]['urunKG'];
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->aracPlaka = $formData[$i]["aracPlaka"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->cikisGumruk = $formData[$i]["cıkısGumruk"];
                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    // Veterineri sistem limite göre atayacak
                    $veteriner = $this->atanacak_veteriner;

                    if (!$urun) {
                        throw new \Exception("Gerekli ilişkili ürün verileri hatalı yada eksik olduğu için evrak kaydı yapılamamıştır, Lütfen gerekli bilgileri doğru bir şekilde doldurup tekrar deneyiniz!");
                    } elseif (!$veteriner) {
                        throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
                    }

                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);

                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $formData[$i]['ss_no'];
                    $saglik_sertfika->toplam_miktar = $formData[$i]['urunKG'];
                    $saglik_sertfika->kalan_miktar = $formData[$i]['urunKG'];
                    $saglik_sertfika->save();
                    $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);



                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('transit');


                    $saved_count++; // Başarıyla eklenen evrak sayısını artır

                }
            } elseif ($formData[0]['evrak_turu'] == 2) {
                for ($i = 1; $i < count($formData); $i++) {
                    $yeni_evrak = new EvrakAntrepoGiris;

                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->vgbOnBildirimNo = $formData[$i]["vgbOnBildirimNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]['urunKG'];
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->aracPlaka = $formData[$i]["aracPlaka"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];


                    // yeni bir antrepo girilmiş ise bunu db ekle
                    $gelen_antrepo = GirisAntrepo::where('name', $formData[$i]["giris_antrepo_id"])->first();
                    if (!$gelen_antrepo) {    // DB de yoksa ekle
                        $gelen_antrepo = new GirisAntrepo;
                        $gelen_antrepo->name = $formData[$i]["giris_antrepo_id"];
                        $gelen_antrepo->save();
                    }
                    $yeni_evrak->giris_antrepo_id = $gelen_antrepo->id;


                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    // Veterineri sistem limite göre atayacak
                    $veteriner = $this->atanacak_veteriner;

                    if (!$urun) {
                        throw new \Exception("Gerekli ilişkili ürün verileri hatalı yada eksik olduğu için evrak kaydı yapılamamıştır, Lütfen gerekli bilgileri doğru bir şekilde doldurup tekrar deneyiniz!");
                    } elseif (!$veteriner) {
                        throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
                    }

                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);

                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $formData[$i]['ss_no'];
                    $saglik_sertfika->toplam_miktar = $formData[$i]['urunKG'];
                    $saglik_sertfika->kalan_miktar = $formData[$i]['urunKG'];
                    $saglik_sertfika->save();
                    $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);



                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('antrepo_giris');


                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 3) {
                for ($i = 1; $i < count($formData); $i++) {




                    // Evrağın atanacağı veteriner sağlık sertifikası üzerinden Antrepo Giriş türü evrağının atandığı veterineri bulma
                    $veteriner = $this->ssn_ile_antrepo_giris_vet_bulma_servisi
                        ->ssn_ile_antrepo_giris_vet_bul($formData, $i);


                    $yeni_evrak = new EvrakAntrepoVaris;
                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->oncekiVGBOnBildirimNo = $formData[$i]["oncekiVGBOnBildirimNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]["urunKG"];
                    $yeni_evrak->urunlerinBulunduguAntrepo = $formData[$i]["urunlerinBulunduguAntrepo"];
                    $yeni_evrak->save();

                    // yeni bir antrepo girilmiş ise bunu db ekle
                    $gelen_antrepo = GirisAntrepo::where('name', $yeni_evrak->urunlerinBulunduguAntrepo)->exists();
                    if (!$gelen_antrepo) {    // DB de yoksa ekle
                        $antrepo = new GirisAntrepo;
                        $antrepo->name = $yeni_evrak->urunlerinBulunduguAntrepo;
                        $antrepo->save();
                    }


                    // Eğer bu veterinerin elinde daha bitmemiş bir evrak varsa sistem random başka bir veterinere atama yapacak
                    $isi_var_mi = $veteriner->evraks->contains(fn($data) => $data->evrak->evrak_durumu->evrak_durum === 'İşlemde');
                    if ($isi_var_mi) {
                        // Veterinerin 50 den fazla elinde işi bitmemiş iş varsa o zaman random bir veteriner seç
                        if ($this->workloads_service->vet_işlemde_worklaod_count($veteriner->id) > 50) {
                            $veteriner = $this->atanacak_veteriner;
                        }
                    } else {


                        // Eğer veterinere evrak sistem tarafından atanmıyorsa manuel olarak workload değerini güncelle
                        $workload = $veteriner->workloads->where('year', $today->year)->first();
                        if (isset($workload)) {
                            $workload->year_workload += 1;
                            $workload->total_workload += 1;
                            $workload->save();
                        }
                    }

                    if (!$veteriner) {
                        throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
                    }


                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);
                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->toplam_miktar = $value['miktar'];
                        $saglik_sertfika->kalan_miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);
                    }

                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('antrepo_varis');


                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 4) {
                for ($i = 1; $i < count($formData); $i++) {

                    $antrepo_giris_saglik_sertifikalari = [];
                    $saglik_sertifikalari = $formData[$i]['vetSaglikSertifikasiNo'];

                    $veterinerSayilari = [];
                    $veterinerSertifikaMiktarlari = [];
                    $veterinerId = 0;

                    // Her sağlık sertifikasının hangi veterinere ait olduğunu belirle
                    foreach ($saglik_sertifikalari as $saglik_sertifika) {

                        // girilen sertifikanın ssn numarası bakarak bu sertifika bir antrepo giriş
                        // evrağı ile ilişkili ise bu sertifikayı alma
                        $ss_saved = SaglikSertifika::where(function ($query) use ($saglik_sertifika) {
                            $query->whereHas('evraks_giris', function ($q) use ($saglik_sertifika) {
                                $q->where('ssn', $saglik_sertifika['ssn']);
                            })->orWhereHas('evraks_varis_dis', function ($q) use ($saglik_sertifika) {
                                $q->where('ssn', $saglik_sertifika['ssn']);
                            });
                        })->with(['evraks_giris.veteriner.user', 'evraks_varis_dis.veteriner.user'])
                            ->first();

                        if (!$ss_saved) {
                            throw new \Exception("Sağlık Sertifikası Numarası Kaydı Sistemde Bulunamadı, Sistemde Kayıtlı Olduğundan Emin Olduktan Sonra Tekrar Deneyiniz!");
                        }

                        /*
                            Girilen ss numarası ile bulunan sağlık sertifikasının miktarından
                            fazla miktarda aynı ss numarası ile bir sağlık sertifikası girilmişse
                            bunu kontrol et , hata ver
                        */
                        if ($ss_saved->kalan_miktar == 0) {
                            throw new \Exception("{$saglik_sertifika['ssn']} numarası ile girilen
                             sağlık sertifikasının kalan miktar bitmiştir(kalmamıştır). Lütfen sistemde kayıtlı sağlık sertifikasının kalan miktarını kontrol ederek
                             tekrar deneyiniz. ");
                        } elseif ($ss_saved->kalan_miktar < $saglik_sertifika['miktar']) {

                            throw new \Exception("{$saglik_sertifika['ssn']} numarası ile girilen
                             sağlık sertifikasının miktar bilgisi sistemde kayıtlı kalan miktardan fazla girilmiş,
                             lütfen sistemde kayıtlı sağlık sertifikasının kalan miktarını kontrol ederek
                             tekrar deneyiniz. ");
                        } else {  // sistemde girilende fazla miktar girmemiş ise(olması gereken)

                            // Gelen sağlık sertifikasının ait olduğu antp giriş evrağının atandığı veterinerini al
                            $veteriner_from_giris = $ss_saved?->evraks_giris?->first()?->veteriner?->user;
                            $veteriner_from_varis_dis = $ss_saved?->evraks_varis_dis?->first()?->veteriner?->user;


                            // Veterinerleri kaşılaştırmak için miktar ve ss sayısını tut
                            if ($veteriner_from_giris) {
                                $vetId = $veteriner_from_giris->id;

                                $kayitli_saglik_sertifikalari[] = $ss_saved;

                                // Veterinerin sahip olduğu sertifika sayısını artır
                                $veterinerSayilari[$vetId] = ($veterinerSayilari[$vetId] ?? 0) + 1;

                                // Veterinerin toplam sağlık sertifikası miktarını artır
                                $veterinerSertifikaMiktarlari[$vetId] = ($veterinerSertifikaMiktarlari[$vetId] ?? 0) + $saglik_sertifika['miktar'];
                            } elseif ($veteriner_from_varis_dis) {
                                $vetId = $veteriner_from_varis_dis->id;

                                $kayitli_saglik_sertifikalari[] = $ss_saved;

                                // Veterinerin sahip olduğu sertifika sayısını artır
                                $veterinerSayilari[$vetId] = ($veterinerSayilari[$vetId] ?? 0) + 1;

                                // Veterinerin toplam sağlık sertifikası miktarını artır
                                $veterinerSertifikaMiktarlari[$vetId] = ($veterinerSertifikaMiktarlari[$vetId] ?? 0) + $saglik_sertifika['miktar'];
                            } else {
                                throw new \Exception("Sağlık Sertifikası Numarası Kaydının bağlı olduğu evrağa atanmış bir veteriner bulunamadı, Sistemde Kayıtlı Evrak Bilgilerinden Emin Olduktan Sonra Tekrar Deneyiniz!");
                            }
                        }
                    }


                    // Sağlık sertifikaları hatalı ise
                    if (empty($veterinerSayilari)) {
                        throw new \Exception("Hiçbir veteriner için sağlık sertifikası bulunamadı!");
                    }





                    // En çok sağlık sertifikasına sahip veterinerleri bul
                    $maxSertifikaSayisi = max($veterinerSayilari);
                    $enCokSertifikaSahipleri = array_keys($veterinerSayilari, $maxSertifikaSayisi);



                    // Eğer en çok sertifika sayısına sahip tek veteriner varsa
                    if (count($enCokSertifikaSahipleri) === 1) {
                        $veterinerId = $enCokSertifikaSahipleri[0];


                        // Veterinerin 50 den fazla elinde işi bitmemiş iş varsa o zaman random bir veteriner seç
                        if ($this->workloads_service->vet_işlemde_worklaod_count($veterinerId) > 50) {
                            $veteriner = $this->atanacak_veteriner;
                            $veterinerId = $veteriner->id;
                        }


                        // Eğer birden fazla veteriner eşitse, miktar toplamına göre karar ver
                    } else {

                        // en fazla ss'da miktara sahip olan veterineri bulma
                        $veterinerId = collect($enCokSertifikaSahipleri)
                            ->sortByDesc(fn($vetId) => $veterinerSertifikaMiktarlari[$vetId])
                            ->first();


                        // Eğer seçilen veterinerin elinde bitmemiş bir evrak varsa 2. sıradakini seç
                        if ($this->workloads_service->vet_işlemde_worklaod_count($veterinerId) > 50) {

                            $veterinerId = $enCokSertifikaSahipleri[1];

                            // Eğer seçilen veterinerin elinde bitmemiş bir evrak varsa sistem atama yapsın
                            if ($this->workloads_service->vet_işlemde_worklaod_count($veterinerId) > 50) {

                                $veteriner = $this->atanacak_veteriner;
                                $veterinerId = $veteriner->id;
                            }
                        }
                    }

                    $veteriner = User::find($veterinerId);

                    if (!$veteriner) {
                        throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
                    }



                    $yeni_evrak = new EvrakAntrepoSertifika;

                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->vgbNo = $formData[$i]["vgbNo"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]["urunKG"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->aracPlaka = $formData[$i]["aracPlaka"];


                    // yeni bir antrepo girilmiş ise bunu db ekle
                    $gelen_antrepo = GirisAntrepo::where('name', $formData[$i]["cikis_antrepo"])->first();
                    if (!$gelen_antrepo) {    // DB de yoksa ekle
                        $gelen_antrepo = new GirisAntrepo;
                        $gelen_antrepo->name = $formData[$i]["cikis_antrepo"];
                        $gelen_antrepo->save();
                    }
                    $yeni_evrak->cikisAntrepo = $gelen_antrepo->name;


                    $evrak_saved = $yeni_evrak->save();
                    if (!$evrak_saved) {
                        throw new \Exception("Evrak Bilgileri Yanlış Yada Hatalı! Lüsfen Bilgileri Kontrol Edip Tekrar Deneyiniz.");
                    }


                    // Antrepo sertifika oluşturulduğunda en son kayıtlı usks numarasın güncelleyerek(yıl ve sondakil sayıyı) bu sertifika ile ilişkilendir.
                    $yil = $today->year; // Değişecek kısım
                    // hiç yoksa oluştur
                    $son_kayitli_usks = UsksNo::latest()?->first()?->usks_no ?? sprintf('33VSKN01.USKS.%d-%04d', $yil, 0); //"33VSKN01.USKS.2025-0475"
                    $parcalar = explode('-', $son_kayitli_usks);
                    $numara = (int)end($parcalar); // Son parçayı al
                    $sonuc = sprintf('33VSKN01.USKS.%d-%04d', $yil, $numara + 1); // sondaki numarayı 1 arttırma
                    // USKS NUMARASI OLUŞTURMA-İLİŞKİLENDİRME
                    $usks = new UsksNo;
                    $usks->usks_no =  $sonuc;
                    $usks->miktar = $yeni_evrak->urunKG;
                    $yeni_evrak->usks()->save($usks);


                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);
                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrağı ilişkilendirme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);

                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->toplam_miktar = $value['miktar'];
                        $saglik_sertfika->kalan_miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);


                        /*
                            antrepo giriş evrağından girilen sertifikalar için bu sertifikalrın
                            miktarlarının antrpeo sertifika evrağında girilen sertifikalar kadar
                            miktarlarının azaltılması için bu evrak oluşturma sırasında girilen
                            aynı ssn numarasına sahip sağlık sertifikaları bulunması sağlandı
                        */
                        foreach ($kayitli_saglik_sertifikalari as $sertifika) {
                            if ($sertifika->ssn == $value['ssn']) {
                                $saglik_sertfika->kalan_miktar = $sertifika->kalan_miktar - $value['miktar'];
                                $sertifika->kalan_miktar = $sertifika->kalan_miktar - $value['miktar'];
                                $sertifika->save();
                                $saglik_sertfika->save();
                            }
                        }
                    }




                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('antrepo_sertifika');


                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 5) {
                for ($i = 1; $i < count($formData); $i++) {


                    // Veterineri usks numarası üzerinden antrepo sertifika bulunarak bu evrağı alan veterinere atanacak
                    $usks = UsksNo::where('usks_no', $formData[$i]['usks_no'])
                        ->with('evrak_antrepo_sertifika')->first();
                    if (!$usks) {
                        throw new \Exception('Girilen USKS bilgilerinin doğru olduğundan emin olduktan sonra tekrar deneyiniz!');
                    }

                    $veteriner = $usks->evrak_antrepo_sertifika->veteriner->user;

                    // Seçilen veterinerin elinde iş varsa atama sistemi tarafından veteriner atama
                    if ($this->workloads_service->vet_işlemde_worklaod_count($veteriner->id) > 50) {
                        $veteriner = $this->atanacak_veteriner;
                    }

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    if (!$urun) {
                        throw new \Exception("Gerekli ilişkili ürün verileri hatalı yada eksik olduğu için evrak kaydı yapılamamıştır, Lütfen gerekli bilgileri doğru bir şekilde doldurup tekrar deneyiniz!");
                    } elseif (!$veteriner) {
                        throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
                    }

                    $yeni_evrak = new EvrakAntrepoCikis;
                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->vgbOnBildirimNo = $formData[$i]["vgbOnBildirimNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]['urunKG'];
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->aracPlaka = $formData[$i]["aracPlaka"];
                    $yeni_evrak->usks_id = $usks->id;   // Bulunan usks'nin id'sini tutmak için düzenleme sırasında
                    $yeni_evrak->cikisGumruk = $formData[$i]["cıkısGumruk"];
                    $yeni_evrak->save();



                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);
                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('antrepo_cikis');


                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 6) {
                for ($i = 1; $i < count($formData); $i++) {

                    $yeni_evrak = new EvrakCanliHayvan;

                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->vgbOnBildirimNo = $formData[$i]["vgbOnBildirimNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->hayvanSayisi = $formData[$i]["hayvanSayisi"];
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->cikisGumruk = $formData[$i]["cıkısGumruk"];
                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    // Veterineri sistem limite göre atayacak
                    $veteriner = $this->atanacak_veteriner;

                    if (!$urun) {
                        throw new \Exception("Gerekli ilişkili ürün verileri hatalı yada eksik olduğu için evrak kaydı yapılamamıştır, Lütfen gerekli bilgileri doğru bir şekilde doldurup tekrar deneyiniz!");
                    } elseif (!$veteriner) {
                        throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
                    }

                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);
                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->toplam_miktar = $value['miktar'];
                        $saglik_sertfika->kalan_miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);
                    }

                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('canli_hayvan');


                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 7) {
                for ($i = 1; $i < count($formData); $i++) {

                    $yeni_evrak = new EvrakCanliHayvanGemi;

                    $yeni_evrak->hayvan_sayisi = $formData[$i]["hayvan_sayisi"];
                    $yeni_evrak->start_date = Carbon::createFromFormat('m/d/Y', $formData[$i]["start_date"]);
                    $yeni_evrak->day_count = (int)$formData[$i]["day_count"];
                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama



                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $formData[$i]["veteriner_id"];
                    $user_evrak->evrak()->associate($yeni_evrak);
                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);


                    // Gemi izni oluşturma
                    $this->gemi_izni_olusturma->canli_h_gemi_izin_olustur(
                        $formData[$i]["veteriner_id"],
                        $yeni_evrak->start_date,
                        (int)$formData[$i]["day_count"],
                        $yeni_evrak->id
                    );

                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 8) {    // Antrepo Varış(DIŞ)
                for ($i = 1; $i < count($formData); $i++) {




                    // Veterineri sistem limite göre atayacak
                    $veteriner = $this->atanacak_veteriner;
                    if (!$veteriner) {
                        throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
                    }

                    $yeni_evrak = new EvrakAntrepoVarisDis;
                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->oncekiVGBOnBildirimNo = $formData[$i]["oncekiVGBOnBildirimNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]["urunKG"];


                    // yeni bir antrepo girilmiş ise bunu db ekle
                    $gelen_antrepo = GirisAntrepo::where('name', $formData[$i]["urunlerinBulunduguAntrepo"])->first();
                    if (!$gelen_antrepo) {    // DB de yoksa ekle
                        $gelen_antrepo = new GirisAntrepo;
                        $gelen_antrepo->name = $formData[$i]["urunlerinBulunduguAntrepo"];
                        $gelen_antrepo->save();
                    }
                    $yeni_evrak->giris_antrepo_id = $gelen_antrepo->id;

                    $yeni_evrak->save();


                    // Veterinerin 50 den fazla elinde işi bitmemiş iş varsa o zaman random bir veteriner seç
                    if ($this->workloads_service->vet_işlemde_worklaod_count($veteriner->id) > 50) {
                        $veteriner = $this->atanacak_veteriner;
                    } else {

                        // Eğer veterinere evrak sistem tarafından atanmıyorsa manuel olarak workload değerini güncelle
                        $workload = $veteriner->workloads->where('year', $today->year)->first();
                        if (isset($workload)) {
                            $workload->year_workload += 1;
                            $workload->total_workload += 1;
                            $workload->save();
                        }
                    }

                    if (!$veteriner) {
                        throw new \Exception("Boşta veteriner bulunamadığı için evrak kaydı yapılamamıştır, Lütfen müsait veteriner olduğundan emin olduktan sonra tekrar deneyiniz!");
                    }


                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);
                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->toplam_miktar = $value['miktar'];
                        $saglik_sertfika->kalan_miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);
                    }

                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('antrepo_varis');


                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            }


            // Veritabanı üzerinde yapılan değişiklikleri kalıcı hale getirme
            DB::commit();

            return redirect()->route('memur.evrak.index')->with('success', "$saved_count evrak başarıyla eklendi.");
        } catch (\Exception $e) {

            // Hata durumunda veritabanını transection(başlangıç) durumundaki haline geri getirir
            DB::rollBack();

            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
    }


    public function get_evrak_sertifika(Request $request)
    {

        try {
            $usks = UsksNo::where('usks_no', $request->input("usks_no"))
                ->with(['evrak_antrepo_sertifika', 'evrak_antrepo_sertifika.saglikSertifikalari', 'evrak_antrepo_sertifika.urun', 'evrak_antrepo_sertifika.veteriner.user'])->first();

            if (!$usks) {
                return response()->json(['success' => false, 'message' => 'Girilen USKS bilgileri herhangi bir evrak ile eşleşmiyor, sistemden kontrol edip doğru olduğundan emin olduktan sonra tekrar deneyiniz.'], 404);
            }

            $sertifika = $usks->evrak_antrepo_sertifika;
            $saglik_sertifikalari = $usks->evrak_antrepo_sertifika->saglikSertifikalari;

            return response()->json(['success' => true, 'sertifika' => $sertifika, 'saglik_sertifikalari' => $saglik_sertifikalari]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }

    // Memurun evrak düzenleme yetkisi yok
}
