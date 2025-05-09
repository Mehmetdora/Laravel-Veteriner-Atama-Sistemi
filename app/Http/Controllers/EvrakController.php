<?php

namespace App\Http\Controllers;

use App\Providers\CanliHGemiIzniOlusturma;
use Carbon\Carbon;
use App\Models\Urun;
use App\Models\User;
use App\Models\UsksNo;
use App\Models\UserEvrak;
use App\Models\EvrakDurum;
use App\Models\AracPlakaKg;
use App\Models\EvrakIthalat;
use App\Models\EvrakTransit;
use Exception;
use Illuminate\Http\Request;
use App\Models\SaglikSertifika;
use App\Providers\AtamaServisi;
use App\Models\EvrakCanliHayvan;
use App\Models\EvrakAntrepoCikis;
use App\Models\EvrakAntrepoGiris;
use App\Models\EvrakAntrepoVaris;
use App\Models\DailyTotalWorkload;
use Illuminate\Support\Facades\DB;
use App\Models\EvrakAntrepoSertifika;
use App\Models\EvrakCanliHayvanGemi;
use App\Models\GirisAntrepo;
use Illuminate\Support\Facades\Validator;
use App\Providers\YeniYilWorkloadsGuncelleme;
use App\Providers\VeterinerEvrakDurumularıKontrolu;
use App\Providers\OrtalamaGunlukWorkloadDegeriBulma;
use App\Providers\TelafiBoyuncaTempWorkloadGuncelleme;
use App\Providers\DailyTotalWorkloadUpdateORCreateService;
use App\Providers\EvrakVeterineriDegisirseWorkloadGuncelleme;
use App\Providers\SsnKullanarakAntrepo_GVeterineriniBulma;

class EvrakController extends Controller
{

    protected $gemi_izni_olusturma;
    protected $ssn_ile_antrepo_giris_vet_bulma_servisi;
    protected $veteriner_evrak_durum_kontrol_servisi;
    protected $daily_total_worklaod_update_create_servisi;
    protected $ortalama_gunluk_workload_degeri_bulma;
    protected $atamaServisi;
    protected $yeni_yil_workloads_guncelleme;
    protected $temp_worloads_updater;
    protected $atanacak_veteriner;
    protected $evrak_vet_degisirse_worklaods_updater;


    function __construct(CanliHGemiIzniOlusturma $canliHGemiIzniOlusturma, EvrakVeterineriDegisirseWorkloadGuncelleme $evrak_veterineri_degisirse_workload_guncelleme, TelafiBoyuncaTempWorkloadGuncelleme $telafiBoyuncaTempWorkloadGuncelleme, YeniYilWorkloadsGuncelleme $yeni_yil_workloads_guncelleme, AtamaServisi $atamaServisi, OrtalamaGunlukWorkloadDegeriBulma $ortalama_gunluk_workload_degeri_bulma, DailyTotalWorkloadUpdateORCreateService $daily_total_workload_update_orcreate_service, VeterinerEvrakDurumularıKontrolu $veterinerEvrakDurumularıKontrolu, SsnKullanarakAntrepo_GVeterineriniBulma $ssn_kullanarak_antrepo_gveterinerini_bulma)
    {
        $this->gemi_izni_olusturma = $canliHGemiIzniOlusturma;
        $this->evrak_vet_degisirse_worklaods_updater = $evrak_veterineri_degisirse_workload_guncelleme;
        $this->temp_worloads_updater = $telafiBoyuncaTempWorkloadGuncelleme;
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
            ->merge(EvrakAntrepoSertifika::with(['veteriner.user', 'usks', 'urun', 'evrak_durumu'])->get())
            ->merge(EvrakAntrepoCikis::with(['veteriner.user', 'urun', 'evrak_durumu'])->get());

        // `created_at`'e göre azalan sırayla sıralama
        $evraks_all = $evraks_all->sortByDesc('created_at');
        $data['evraks_all'] = $evraks_all;


        return view('admin.evrak_kayit.index', $data);
    }

    public function detail($type, $evrak_id)
    {

        $type = explode("\\", $type);
        $type = end($type);

        if ($type == "EvrakIthalat") {
            $data['evrak'] = EvrakIthalat::with(['urun', 'aracPlakaKgs', 'veteriner.user', 'evrak_durumu'])
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
        } else if ($type == "EvrakAntrepoSertifika") {
            $data['evrak'] = EvrakAntrepoSertifika::with(['urun', 'veteriner.user',  'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoCikis") {
            $data['evrak'] = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakCanliHayvan") {
            $data['evrak'] = EvrakCanliHayvan::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        }

        $data['type'] = $type;


        return view('admin.evrak_kayit.detail', $data);
    }


    public function create()
    {
        $data['giris_antrepos'] = GirisAntrepo::actives();
        $data['uruns'] = Urun::all();
        $data['veteriners'] = User::role('veteriner')->where('status', 1)->get();
        return view('admin.evrak_kayit.create', $data);
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
                    'siraNo' => 'required',
                    'vgbOnBildirimNo' => 'required',
                    'ss_no' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => 'required',
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'arac_plaka_kg' => 'required',
                    'girisGumruk' => 'required',
                    'cıkısGumruk' => 'required',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 1) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required',
                    'vgbOnBildirimNo' => 'required',
                    'ss_no' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => 'required',
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'aracPlaka' => 'required',
                    'girisGumruk' => 'required',
                    'cıkısGumruk' => 'required',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 2) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required',
                    'vgbOnBildirimNo' => 'required',
                    'ss_no' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => 'required',
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'aracPlaka' => 'required',
                    'girisGumruk' => 'required',
                    'giris_antrepo_id' => 'required',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 3) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required',
                    'oncekiVGBOnBildirimNo' => 'required',
                    'vetSaglikSertifikasiNo' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => 'required',
                    'urunlerinBulunduguAntrepo' => 'required',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 4) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required',
                    'vetSaglikSertifikasiNo' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => 'required',
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'aracPlaka' => 'required',
                    'girisGumruk' => 'required',
                    'cıkısGumruk' => 'required',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 5) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required',
                    'vgbOnBildirimNo' => 'required',
                    'usks_no' => 'required',
                    'usks_miktar' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'urunKG' => 'required',
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'aracPlaka' => 'required',
                    'cıkısGumruk' => 'required',
                ]);
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->all();
                }
            }
        } elseif ($formData[0]['evrak_turu'] == 6) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required',
                    'vgbOnBildirimNo' => 'required',
                    'vetSaglikSertifikasiNo' => 'required',
                    'vekaletFirmaKisiAdi' => 'required',
                    'urunAdi' => 'required',
                    'urun_kategori_id' => 'required',
                    'gtipNo' => 'required',
                    'hayvanSayisi' => 'required',
                    'sevkUlke' => 'required',
                    'orjinUlke' => 'required',
                    'girisGumruk' => 'required',
                    'cıkısGumruk' => 'required',
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
            default:
                return redirect()->back()->withErrors($errors)->with('error', 'Hatalı evrak türü seçiminden dolayı evrak oluşturulamamıştır, Lütfen tekrar deneyiniz!');
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
                    $yeni_evrak->urunKG = (int)str_replace('.', '', $formData[$i]['urunKG']);
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->cikisGumruk = $formData[$i]["cıkısGumruk"];
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
                    $saglik_sertfika->toplam_miktar = (int)str_replace('.', '', $formData[$i]['urunKG']);
                    $saglik_sertfika->kalan_miktar = (int)str_replace('.', '', $formData[$i]['urunKG']);
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
                    $yeni_evrak->urunKG = (int)str_replace('.', '', $formData[$i]['urunKG']);
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
                    $saglik_sertfika->toplam_miktar = (int)str_replace('.', '', $formData[$i]['urunKG']);
                    $saglik_sertfika->kalan_miktar = (int)str_replace('.', '', $formData[$i]['urunKG']);
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
                    $yeni_evrak->urunKG = (int)str_replace('.', '', $formData[$i]['urunKG']);
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->aracPlaka = $formData[$i]["aracPlaka"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->giris_antrepo_id = $formData[$i]["giris_antrepo_id"];
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
                    $saglik_sertfika->toplam_miktar = (int)str_replace('.', '', $formData[$i]['urunKG']);
                    $saglik_sertfika->kalan_miktar = (int)str_replace('.', '', $formData[$i]['urunKG']);
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



                    // Eğer bu veterinerin elinde daha bitmemiş bir evrak varsa sistem random başka bir veterinere atama yapacak
                    $isi_var_mi = $veteriner->evraks->contains(fn($data) => $data->evrak->evrak_durumu->evrak_durum === 'İşlemde');
                    if ($isi_var_mi) {
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
                        $ss_saved = SaglikSertifika::whereHas('evraks_giris', function ($query) {})
                            ->where('ssn', $saglik_sertifika['ssn'])
                            ->with(['evraks_giris.veteriner.user'])
                            ->first();

                        // Gelen sağlık sertifikasının ait olduğu antp giriş evrağının veterinerini al
                        $veteriner = $ss_saved?->evraks_giris?->first()?->veteriner?->user;


                        // Veterinerleri kaşılaştırmak için miktar ve ss sayısını tut
                        if ($veteriner) {
                            $vetId = $veteriner->id;

                            $antrepo_giris_saglik_sertifikalari[] = $ss_saved;

                            // Veterinerin sahip olduğu sertifika sayısını artır
                            $veterinerSayilari[$vetId] = ($veterinerSayilari[$vetId] ?? 0) + 1;

                            // Veterinerin toplam sağlık sertifikası miktarını artır
                            $veterinerSertifikaMiktarlari[$vetId] = ($veterinerSertifikaMiktarlari[$vetId] ?? 0) + $saglik_sertifika['miktar'];
                        } else {
                            throw new \Exception("Sağlık Sertifikası Numarası Bulunamadı, Sistemde Kayıtlı Olduğundan Emin Olduktan Sonra Tekrar Deneyiniz!");
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


                        // Eğer seçilen veterinerin elinde bitmemiş bir evrak varsa
                        if ($this->veteriner_evrak_durum_kontrol_servisi->vet_evrak_durum_kontrol($veterinerId)) {

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
                        if ($this->veteriner_evrak_durum_kontrol_servisi->vet_evrak_durum_kontrol($veterinerId)) {

                            $veterinerId = $enCokSertifikaSahipleri[1];

                            // Eğer seçilen veterinerin elinde bitmemiş bir evrak varsa sistem atama yapsın
                            if ($this->veteriner_evrak_durum_kontrol_servisi->vet_evrak_durum_kontrol($veterinerId)) {

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
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]["urunKG"];
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->aracPlaka = $formData[$i]["aracPlaka"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->cikisGumruk = $formData[$i]["cıkısGumruk"];
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
                        foreach ($antrepo_giris_saglik_sertifikalari as $sertifika) {
                            if ($sertifika->ssn == $value['ssn']) {
                                $sertifika->kalan_miktar = $sertifika->toplam_miktar - $value['miktar'];
                                $sertifika->save();
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
                        ->where('miktar', (int)str_replace('.', '', $formData[$i]['usks_miktar']))
                        ->with('evrak_antrepo_sertifika')->first();

                    if (!$usks) {
                        throw new \Exception('Girilen USKS bilgilerinin doğru olduğundan emin olduktan sonra tekrar deneyiniz!');
                    }

                    $veteriner = $usks->evrak_antrepo_sertifika->veteriner->user;

                    // Seçilen veterinerin elinde iş varsa atama sistemi tarafından veteriner atama
                    if ($this->veteriner_evrak_durum_kontrol_servisi->vet_evrak_durum_kontrol($veteriner->id)) {
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
                    $yeni_evrak->urunKG = (int)str_replace('.', '', $formData[$i]['urunKG']);
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
                        (int)$formData[$i]["day_count"]
                    );

                    // Veterinerin worklaod güncelleme
                    $veteriner = User::find($formData[$i]["veteriner_id"]);
                    $workload = $veteriner->veterinerinBuYilkiWorkloadi();
                    if ($yeni_evrak->hayvan_sayisi > 0 && $yeni_evrak->hayvan_sayisi <= 15000) {
                        $workload->year_workload += 150;
                        $workload->total_workload += 150;
                        if ($workload->temp_workload != 0) {
                            $workload->temp_workload += 150;
                        }
                    }elseif($yeni_evrak->hayvan_sayisi > 15000){
                        $workload->year_workload += 300;
                        $workload->total_workload += 300;
                        if ($workload->temp_workload != 0) {
                            $workload->temp_workload += 300;
                        }
                    }
                    $workload->save();


                    $saved_count++; // Başarıyla eklenen evrak sayısını artır

                }
            }


            // Veritabanı üzerinde yapılan değişiklikleri kalıcı hale getirme
            DB::commit();

            return redirect()->route('admin.evrak.index')->with('success', "$saved_count evrak başarıyla eklendi.");
        } catch (\Exception $e) {

            // Hata durumunda veritabanını transection(başlangıç) durumundaki haline geri getirir
            DB::rollBack();

            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
    }

    public function edit($type, $evrak_id)
    {

        $type = explode("\\", $type);
        $type = end($type);

        if ($type == "EvrakIthalat") {
            $data['evrak'] = EvrakIthalat::with(['urun', 'aracPlakaKgs', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakTransit") {
            $data['evrak'] = EvrakTransit::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoGiris") {
            $data['evrak'] = EvrakAntrepoGiris::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoVaris") {
            $data['evrak'] = EvrakAntrepoVaris::with(['veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoSertifika") {
            $data['evrak'] = EvrakAntrepoSertifika::with(['urun', 'veteriner.user',  'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoCikis") {
            $evrak = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
            $data['evrak'] = $evrak;
            $data['usks'] = UsksNo::find($evrak->usks_id);
        } else if ($type == "EvrakCanliHayvan") {
            $data['evrak'] = EvrakCanliHayvan::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        }



        $data['evrak_type'] = $type;
        $data['veteriners'] = User::role('veteriner')->where('status', 1)->get();
        $data['uruns'] = Urun::all();
        $data['giris_antrepos'] = GirisAntrepo::actives();

        return view('admin.evrak_kayit.edit', $data);
    }

    public function edited(Request $request)
    {
        $errors = [];

        // Validation
        if ($request->type == "EvrakIthalat") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'ss_no' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'arac_plaka_kg' => 'required',
                'girisGumruk' => 'required',
                'cikisGumruk' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == 'EvrakTransit') {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'ss_no' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'aracPlaka' => 'required',
                'girisGumruk' => 'required',
                'cikisGumruk' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoGiris") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'ss_no' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'aracPlaka' => 'required',
                'girisGumruk' => 'required',
                'giris_antrepo_id' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoVaris") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'oncekiVGBOnBildirimNo' => 'required',
                'vetSaglikSertifikasiNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'urunlerinBulunduguAntrepo' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoSertifika") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vetSaglikSertifikasiNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'aracPlaka' => 'required',
                'girisGumruk' => 'required',
                'cikisGumruk' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoCikis") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'usks_no' => 'required',
                'usks_miktar' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'aracPlaka' => 'required',
                'cikisGumruk' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakCanliHayvan") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'vetSaglikSertifikasiNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'hayvanSayisi' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'girisGumruk' => 'required',
                'cikisGumruk' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        }

        // Eğer hata varsa, geriye yönlendir ve tüm hataları göster
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->with('error', $errors);
        }


        DB::beginTransaction();

        try {

            if ($request->type == "EvrakIthalat") {


                $evrak = EvrakIthalat::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = (int)str_replace('.', '', $request->urunKG);
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);



                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'ithalat'
                        );
                }
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                // Gelen plaka ID'lerini al
                $yeni_plakalar = [];
                $plakalar = json_decode($request->arac_plaka_kg) ?? [];
                $plaka_ids = [];
                foreach ($plakalar as $plaka) {
                    if (!isset($plaka->id) || $plaka->id == -1) {
                        $yeni_plakalar[] = $plaka;
                    } else {
                        $plaka_ids[] = $plaka->id;
                    }
                }

                // Silinmesi gerekenleri silme
                $evrak->aracPlakaKgs()
                    ->whereNotIn('arac_plaka_kgs.id', $plaka_ids)
                    ->delete();

                foreach ($yeni_plakalar as $plaka) {
                    AracPlakaKg::create([
                        'arac_plaka' => $plaka->plaka,
                        'miktar' => $plaka->miktar,
                        'evrak_ithalat_id' => $evrak->id
                    ]);
                }

                // SErtifika update
                $sertifika = $evrak->saglikSertifikalari()->first();
                $sertifika->ssn = $request->ss_no;
                $sertifika->toplam_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->kalan_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->save();
            } elseif ($request->type == "EvrakTransit") {
                $evrak = EvrakTransit::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = (int)str_replace('.', '', $request->urunKG);
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);


                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'transit'
                        );
                }
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                // SErtifika update
                $sertifika = $evrak->saglikSertifikalari()->first();
                $sertifika->ssn = $request->ss_no;
                $sertifika->toplam_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->kalan_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->save();
            } elseif ($request->type == "EvrakAntrepoGiris") {
                $evrak = EvrakAntrepoGiris::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = (int)str_replace('.', '', $request->urunKG);
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->giris_antrepo_id = $request->giris_antrepo_id;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);


                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'antrepo_giris'
                        );
                }
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                // SErtifika update
                $sertifika = $evrak->saglikSertifikalari()->first();
                $sertifika->ssn = $request->ss_no;
                $sertifika->toplam_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->kalan_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->save();
            } elseif ($request->type == "EvrakAntrepoVaris") {

                $evrak = EvrakAntrepoVaris::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->oncekiVGBOnBildirimNo = $request->oncekiVGBOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->urunlerinBulunduguAntrepo = $request->urunlerinBulunduguAntrepo;
                $evrak->save();

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'antrepo_varis'
                        );
                }
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                //Sağlık sertifikalarını kaydetme
                // Sağlık sertifikalarını silmeden önce hangilerinin silinip hangilerinin kalacağına karar verme

                // Gelen sağlık sertifikalarının ID'lerini al
                $yeni_sertifikalar = [];
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo) ?? [];
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {
                    if (!isset($sertifika->id) || $sertifika->id == -1) {
                        $yeni_sertifikalar[] = $sertifika;
                    } else {
                        $sertifika_ids[] = $sertifika->id;
                    }
                }

                // Silinmesi gerekenleri silme
                $evrak->saglikSertifikalari()
                    ->whereNotIn('saglik_sertifikas.id', $sertifika_ids)
                    ->delete();

                foreach ($yeni_sertifikalar as $sertifika) {

                    $evrak->saglikSertifikalari()->create([
                        'ssn' => $sertifika->ssn,
                        'toplam_miktar' => $sertifika->miktar,
                        'kalan_miktar' => $sertifika->miktar,
                    ]);
                }
            } elseif ($request->type == "EvrakAntrepoSertifika") {


                $evrak = EvrakAntrepoSertifika::find($request->input('id'));
                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }
                $evrak->urun()->sync([$urun->id]);

                $usks = $evrak->usks;
                if (!$usks) {
                    throw new \Exception('Evrakla ilişkili USKS verisine erişilemedi, Lütfen tekrar deneyiniz!');
                }
                $usks->miktar = $request->urunKG;
                $usks->save();


                //Sağlık sertifikalarını kaydetme
                // Sağlık sertifikalarını silmeden önce hangilerinin silinip hangilerinin kalacağına karar verme
                // Gelen sağlık sertifikalarının ID'lerini al
                $yeni_sertifikalar = [];
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo) ?? [];
                if (count($sertifikalar) == 0) {
                    throw new \Exception('Lütfen evrağa en az 1 adet sağlık sertifikası giriniz!');
                }

                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {
                    if (!isset($sertifika->id) || $sertifika->id == -1) {   // yeni gelen sertifika
                        $yeni_sertifikalar[] = $sertifika;
                    } else {        // Zaten kayıtlı sertifikalar
                        $sertifika_ids[] = $sertifika->id;
                    }
                }


                $silinen_sertifikalar = $evrak->saglikSertifikalari()
                    ->whereNotIn('saglik_sertifikas.id', $sertifika_ids)->get();


                // amaç silinen bir sertifikanın miktarını geri antrpeo girişteki sertifikanın miktarına eklemek
                // silinen sertifikalarda ana sertifikaya geri iade işlemi yapılacak
                foreach ($silinen_sertifikalar as $sertifika) {
                    $antp_giris_sertifika = SaglikSertifika::whereHas('evraks_giris', function ($query) {})
                        ->where('ssn', $sertifika->ssn)
                        ->with('evraks_giris')
                        ->first();
                    if ($antp_giris_sertifika->evraks_giris) {    // girilen sertifikaların ssn numaraları ile ana sertifikalar bulunuyor
                        $antp_giris_sertifika->kalan_miktar += $sertifika->toplam_miktar;
                        $antp_giris_sertifika->save();
                    }
                }


                // Sertifikanın miktarını tekrar güncelledikten sonra silinecekler silinebilir
                $evrak->saglikSertifikalari()
                    ->whereNotIn('saglik_sertifikas.id', $sertifika_ids)
                    ->delete();




                // yeni eklenen sertifikalarda ana sertifikadan düşülecek
                // değişiklik yapılmayan sertifikalarda yine bir değişiklik yapılmayacak
                // En son yeni gelen evraklar ekleniyor
                foreach ($yeni_sertifikalar as $sertifika) {

                    $evrak->saglikSertifikalari()->create([
                        'ssn' => $sertifika->ssn,
                        'toplam_miktar' => $sertifika->miktar,
                        'kalan_miktar' => $sertifika->miktar,
                    ]);


                    $giris_sertifikası = SaglikSertifika::whereHas('evraks_giris', function ($query) {})
                        ->where('ssn', $sertifika->ssn)
                        ->with('evraks_giris')
                        ->first();
                    if ($giris_sertifikası) {
                        $giris_sertifikası->kalan_miktar -= $sertifika->miktar;
                        $giris_sertifikası->save();
                    }
                }


                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'antrepo_sertifika'
                        );
                }
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);
            } elseif ($request->type == "EvrakAntrepoCikis") {
                $evrak = EvrakAntrepoCikis::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = (int)str_replace('.', '', $request->urunKG);
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();


                // Veterineri usks numarası üzerinden antrepo sertifika bulunarak bu evrağı alan veterinere atanacak
                $usks = UsksNo::find($evrak->usks_id);
                $usks->usks_no = $request->usks_no;
                $usks->miktar = (int)str_replace('.', '', $request->usks_miktar);
                $usks->save();

                // Ürün modelini bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }
                $evrak->urun()->sync([$urun->id]);

                // Atanacak olan veteriner gelen veteriner hangisi ise ona atanır
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'antrepo_cikis'
                        );
                }
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);
            } elseif ($request->type == "EvrakCanliHayvan") {

                $evrak = EvrakCanliHayvan::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->hayvanSayisi = $request->hayvanSayisi;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);



                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'canli_hayvan'
                        );
                }
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                //Sağlık sertifikalarını kaydetme
                // Sağlık sertifikalarını silmeden önce hangilerinin silinip hangilerinin kalacağına karar verme

                // Gelen sağlık sertifikalarının ID'lerini al
                $yeni_sertifikalar = [];
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo) ?? [];
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {
                    if (!isset($sertifika->id) || $sertifika->id == -1) {
                        $yeni_sertifikalar[] = $sertifika;
                    } else {
                        $sertifika_ids[] = $sertifika->id;
                    }
                }

                // Silinmesi gerekenleri silme
                $evrak->saglikSertifikalari()
                    ->whereNotIn('saglik_sertifikas.id', $sertifika_ids)
                    ->delete();

                foreach ($yeni_sertifikalar as $sertifika) {

                    $evrak->saglikSertifikalari()->create([
                        'ssn' => $sertifika->ssn,
                        'miktar' => $sertifika->miktar,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.evrak.index')->with('success', "Evrak başarıyla düzenlendi.");
        } catch (\Exception $e) {

            DB::rollBack();     // veritabanını eski haline getirme - hata olmsı durumunda
            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
    }
}
