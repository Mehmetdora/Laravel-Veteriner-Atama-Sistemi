<?php

namespace App\Http\Controllers\memur;

use App\Models\EvrakCanliHayvan;
use App\Models\UsksNo;
use App\Providers\AtamaServisi;
use App\Providers\OrtalamaGunlukWorkloadDegeriBulma;
use Carbon\Carbon;
use App\Models\Urun;
use App\Models\User;
use App\Models\UserEvrak;
use App\Models\EvrakDurum;
use App\Models\EvrakIthalat;
use App\Models\EvrakTransit;
use Illuminate\Http\Request;
use App\Models\SaglikSertifika;
use App\Models\EvrakAntrepoCikis;
use App\Models\EvrakAntrepoGiris;
use App\Models\EvrakAntrepoVaris;
use App\Models\EvrakAntrepoSertifika;
use App\Providers\DailyTotalWorkloadUpdateORCreateService;
use App\Providers\SsnKullanarakAntrepo_GVeterineriniBulma;
use App\Providers\VeterinerEvrakDurumularıKontrolu;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;


class EvrakController extends Controller
{

    protected $ssn_ile_antrepo_giris_vet_bulma_servisi;
    protected $veteriner_evrak_durum_kontrol_servisi;
    protected $daily_total_worklaod_update_create_servisi;
    protected $ortalama_gunluk_workload_degeri_bulma;
    protected $atamaServisi;
    function __construct(AtamaServisi $atamaServisi, OrtalamaGunlukWorkloadDegeriBulma $ortalama_gunluk_workload_degeri_bulma, DailyTotalWorkloadUpdateORCreateService $daily_total_workload_update_orcreate_service, VeterinerEvrakDurumularıKontrolu $veterinerEvrakDurumularıKontrolu, SsnKullanarakAntrepo_GVeterineriniBulma $ssn_kullanarak_antrepo_gveterinerini_bulma)
    {
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
            ->merge(EvrakAntrepoSertifika::with(['veteriner.user', 'urun', 'evrak_durumu'])->get())
            ->merge(EvrakAntrepoCikis::with(['veteriner.user', 'urun', 'evrak_durumu'])->get());

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
        } else if ($type == "EvrakAntrepoSertifika") {
            $data['evrak'] = EvrakAntrepoSertifika::with(['urun', 'veteriner.user',  'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoCikis") {
            $evrak = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
            $data['evrak'] = $evrak;
            $data['usks'] = UsksNo::find($evrak->usks_id);
        } else if ($type == "EvrakCanliHayvan") {
            $data['evrak'] = EvrakCanliHayvan::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        }

        $data['type'] = $type;


        return view('memur.evrak_kayit.detail', $data);
    }


    public function create()
    {

        $data['uruns'] = Urun::all();
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
        $today = now()->setTimezone('Europe/Istanbul'); // tam saat

        $formData = json_decode($request->formData, true); // JSON stringi diziye çeviriyoruz


        if (!$formData) {
            return redirect()->back()->with('error', 'Geçersiz veri formatı!');
        }


        // İlk gelen formdaki evrağın türü ne ise diğerleride aynı türde olduğunu
        // varsayarak evrak türünü belirleyip tüm evrakları for ile özel validate işlemi uygulandı
        $errors = [];

        // Validation
        if ($formData[0]['evrak_turu'] == 0 || $formData[0]['evrak_turu'] == 1) {
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
                    'varisAntreposu' => 'required',
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
        }

        // Eğer hata varsa, geriye yönlendir ve tüm hataları göster
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->with('error', $errors);
        }


        //EĞER TEK SEFERDE GELEN EVRAK SAYISI 1 DEN FAZLA İSE TÜM EVRAKLARI LİMİTE GÖRE BAKIP TEK BİR VETERİNERE ATANMASI GEREKİYOR.
        $gelen_evrak_sayisi = count($formData) - 1;



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
                    $yeni_evrak->urunKG = $formData[$i]["urunKG"];
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->aracPlaka = $formData[$i]["aracPlaka"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->cikisGumruk = $formData[$i]["cıkısGumruk"];
                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    // Veterineri sistem limite göre atayacak


                    $veteriner = $this->atamaServisi->assignVet('ithalat');


                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);
                    $saved = $user_evrak->save();

                    if (!$saved) {
                        throw new \Exception("Evrak kaydedilemedi!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikasını kaydetme
                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $formData[$i]['ss_no'];
                    $saglik_sertfika->miktar = $formData[$i]['urunKG'];
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
                    $yeni_evrak->urunKG = $formData[$i]["urunKG"];
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->aracPlaka = $formData[$i]["aracPlaka"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->cikisGumruk = $formData[$i]["cıkısGumruk"];
                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    // Veterineri sistem limite göre atayacak
                    $veteriner = $this->atamaServisi->assignVet('transit');

                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);

                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydedilemedi!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $formData[$i]['ss_no'];
                    $saglik_sertfika->miktar = $formData[$i]['urunKG'];
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
                    $yeni_evrak->urunKG = $formData[$i]["urunKG"];
                    $yeni_evrak->sevkUlke = $formData[$i]["sevkUlke"];
                    $yeni_evrak->orjinUlke = $formData[$i]["orjinUlke"];
                    $yeni_evrak->aracPlaka = $formData[$i]["aracPlaka"];
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->varisAntreposu = $formData[$i]["varisAntreposu"];
                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    // Veterineri sistem limite göre atayacak
                    $veteriner = $this->atamaServisi->assignVet('antrepo_giris');

                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);

                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydedilemedi!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $formData[$i]['ss_no'];
                    $saglik_sertfika->miktar = $formData[$i]['urunKG'];
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
                        $veteriner = $this->atamaServisi->assignVet('antrepo_varis');
                    } else {


                        // Eğer veterinere evrak sistem tarafından atanmıyorsa manuel olarak workload değerini güncelle
                        $workload = $veteriner->workloads->where('year', $today->year)->first();
                        if (isset($workload)) {
                            $workload->year_workload += 1;
                            $workload->total_workload += 1;
                            $workload->save();
                        }
                    }


                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);
                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydedilemedi!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);
                    }

                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('antrepo_varis');

                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 4) {
                for ($i = 1; $i < count($formData); $i++) {


                    $saglik_sertifikalari = $formData[$i]['vetSaglikSertifikasiNo'];

                    $veterinerSayilari = [];
                    $veterinerSertifikaMiktarlari = [];
                    $veterinerId = 0;

                    // Her sağlık sertifikasının hangi veterinere ait olduğunu belirle
                    foreach ($saglik_sertifikalari as $saglik_sertifika) {
                        $ss_saved = SaglikSertifika::where('ssn', $saglik_sertifika['ssn'])
                            ->where('miktar', $saglik_sertifika['miktar'])
                            ->with(['evraks_giris.veteriner.user'])
                            ->first();

                        // Gelen sağlık sertifikasının ait olduğu antp giriş evrağının veterinerini al
                        $veteriner = $ss_saved?->evraks_giris?->first()?->veteriner?->user;


                        // Veterinerleri kaşılaştırmak için miktar ve ss sayısını tut
                        if ($veteriner) {
                            $vetId = $veteriner->id;

                            // Veterinerin sahip olduğu sertifika sayısını artır
                            $veterinerSayilari[$vetId] = ($veterinerSayilari[$vetId] ?? 0) + 1;

                            // Veterinerin toplam sağlık sertifikası miktarını artır
                            $veterinerSertifikaMiktarlari[$vetId] = ($veterinerSertifikaMiktarlari[$vetId] ?? 0) + $saglik_sertifika['miktar'];
                        } else {
                            throw new \Exception("Sağlık Sertifikası Numarası Bulunamadı, Sistemde Kayıtlı Olduğundan Emin Olduktan Sonra Tekrar Deneyiniz!");
                        }
                    }


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

                            $veteriner = $this->atamaServisi->assignVet('antrepo_sertifika');
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

                                $veteriner = $this->atamaServisi->assignVet('antrepo_sertifika');
                                $veterinerId = $veteriner->id;
                            }
                        }
                    }

                    $veteriner = User::find($veterinerId);

                    if (!$veteriner) {
                        throw new \Exception("Seçilen veteriner sistemde bulunamadı!");
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
                    $sonuc = sprintf('33VSKN01.USKS.%d-%04d', $yil, $numara + 1);
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
                        throw new \Exception("Evrak kaydedilemedi!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);
                    }

                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('antrepo_sertifika');

                    $saved_count++; // Başarıyla eklenen evrak sayısını artır

                }
            } elseif ($formData[0]['evrak_turu'] == 5) {
                for ($i = 1; $i < count($formData); $i++) {


                    // Veterineri usks numarası üzerinden antrepo sertifika bulunarak bu evrağı alan veterinere atanacak
                    $usks = UsksNo::where('usks_no', $formData[$i]['usks_no'])->where('miktar', $formData[$i]['usks_miktar'])->with('evrak_antrepo_sertifika')->first();
                    $veteriner = $usks->evrak_antrepo_sertifika->veteriner->user;

                    // Seçilen veterinerin elinde iş varsa atama sistemi tarafından veteriner atama
                    if ($this->veteriner_evrak_durum_kontrol_servisi->vet_evrak_durum_kontrol($veteriner->id)) {
                        $veteriner = $this->atamaServisi->assignVet('antrepo_cikis');
                    }

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak = new EvrakAntrepoCikis;
                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->vgbOnBildirimNo = $formData[$i]["vgbOnBildirimNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]["urunKG"];
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
                        throw new \Exception("Evrak kaydedilemedi!");
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
                    $veteriner = $this->atamaServisi->assignVet('canli_hayvan');

                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak->setUrun($urun);

                    // Veteriner ile evrak kaydetme
                    $user_evrak = new UserEvrak;
                    $user_evrak->user_id = $veteriner->id;
                    $user_evrak->evrak()->associate($yeni_evrak);

                    $saved = $user_evrak->save();
                    if (!$saved) {
                        throw new \Exception("Evrak kaydedilemedi!");
                    }

                    // Evrak durumunu kaydetme
                    $evrak_durum = new EvrakDurum;
                    $yeni_evrak->evrak_durumu()->save($evrak_durum);

                    //Sağlık sertifikalarını kaydetme
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);
                    }

                    // Günlük gelen evrakların toplam workload değerini tutma servisi
                    $this->daily_total_worklaod_update_create_servisi->updateOrCreateTodayWorkload('canli_hayvan');

                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            }



            return redirect()->route('memur.evrak.index')->with('success', "$saved_count evrak başarıyla eklendi.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
    }

/*     public function edit($type, $evrak_id)
    {

        $type = explode("\\", $type);
        $type = end($type);

        if ($type == "EvrakIthalat") {
            $data['evrak'] = EvrakIthalat::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
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

        return view('admin.evrak_kayit.edit', $data);
    }

    public function edited(Request $request)
    {


        $errors = [];

        // Validation
        if ($request->type == "EvrakIthalat" || $request->type == "EvrakTransit") {
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
                'varisAntreposu' => 'required',
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


        try {

            if ($request->type == "EvrakIthalat") {

                $evrak = EvrakIthalat::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
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



                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
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


                // Silinmesi gerekenleri silme
                $evrak->saglikSertifikalari()->delete();

                $evrak->saglikSertifikalari()->create([
                    'ssn' => $request->ss_no,
                    'miktar' => $request->urunKG,
                ]);

            } elseif ($request->type == "EvrakTransit") {
                $evrak = EvrakTransit::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
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

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
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

                // Silinmesi gerekenleri silme
                $evrak->saglikSertifikalari()->delete();

                $evrak->saglikSertifikalari()->create([
                    'ssn' => $request->ss_no,
                    'miktar' => $request->urunKG,
                ]);


            } elseif ($request->type == "EvrakAntrepoGiris") {
                $evrak = EvrakAntrepoGiris::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->varisAntreposu = $request->varisAntreposu;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);


                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
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

                // Silinmesi gerekenleri silme
                $evrak->saglikSertifikalari()->delete();

                $evrak->saglikSertifikalari()->create([
                    'ssn' => $request->ss_no,
                    'miktar' => $request->urunKG,
                ]);


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

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
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
            } elseif ($request->type == "EvrakAntrepoCikis") {
                $evrak = EvrakAntrepoCikis::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();


                // Veterineri usks numarası üzerinden antrepo sertifika bulunarak bu evrağı alan veterinere atanacak
                $usks = UsksNo::find($evrak->usks_id);
                $usks->usks_no = $request->usks_no;
                $usks->miktar = $request->usks_miktar;
                $usks->save();

                // Ürün modelini bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }
                $evrak->urun()->sync([$urun->id]);

                // Atanacak olan veteriner gelen veteriner hangisi ise ona atanır
                $user_evrak = $evrak->veteriner;
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

            return redirect()->route('admin.evrak.index')->with('success', "Evrak başarıyla düzenlendi.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
    } */
}
