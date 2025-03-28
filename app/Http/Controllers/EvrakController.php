<?php

namespace App\Http\Controllers;


use App\Models\EvrakCanliHayvan;
use App\Providers\AtamaServisi;
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
use Illuminate\Support\Facades\Validator;

class EvrakController extends Controller
{

    protected $atamaServisi;
    function __construct(AtamaServisi $atamaServisi)
    {

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


        return view('admin.evrak_kayit.index', $data);
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

        $data['uruns'] = Urun::all();
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
        } elseif ($formData[0]['evrak_turu'] == 2) {
            for ($i = 1; $i < count($formData); $i++) {
                $validator = Validator::make($formData[$i], [
                    'siraNo' => 'required',
                    'vgbOnBildirimNo' => 'required',
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
                    'USKSSertifikaReferansNo' => 'required',
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
                    'vetSaglikSertifikasiNo' => 'required',
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

                    //Sağlık sertifikalarını kaydetme
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);
                    }
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
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);
                    }
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
                    foreach ($formData[$i]['vetSaglikSertifikasiNo'] as $value) {
                        $saglik_sertfika = new SaglikSertifika;
                        $saglik_sertfika->ssn = $value['ssn'];
                        $saglik_sertfika->miktar = $value['miktar'];
                        $saglik_sertfika->save();
                        $yeni_evrak->saglikSertifikalari()->attach($saglik_sertfika->id);
                    }
                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 3) {
                for ($i = 1; $i < count($formData); $i++) {
                    $yeni_evrak = new EvrakAntrepoVaris;

                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->oncekiVGBOnBildirimNo = $formData[$i]["oncekiVGBOnBildirimNo"];
                    $yeni_evrak->vekaletFirmaKisiAdi = $formData[$i]["vekaletFirmaKisiAdi"];
                    $yeni_evrak->urunAdi = $formData[$i]["urunAdi"];
                    $yeni_evrak->gtipNo = $formData[$i]["gtipNo"];
                    $yeni_evrak->urunKG = $formData[$i]["urunKG"];
                    $yeni_evrak->urunlerinBulunduguAntrepo = $formData[$i]["urunlerinBulunduguAntrepo"];
                    $yeni_evrak->save();

                    // Veterineri sistem limite göre atayacak
                    $veteriner = $this->atamaServisi->assignVet('antrepo_varis');

                    if (!$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
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
                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 4) {
                for ($i = 1; $i < count($formData); $i++) {
                    $yeni_evrak = new EvrakAntrepoSertifika;

                    $yeni_evrak->evrakKayitNo = $formData[$i]["siraNo"];
                    $yeni_evrak->USKSSertifikaReferansNo = $formData[$i]["USKSSertifikaReferansNo"];
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
                    $veteriner = $this->atamaServisi->assignVet('antrepo_sertifika');

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
                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            } elseif ($formData[0]['evrak_turu'] == 5) {
                for ($i = 1; $i < count($formData); $i++) {
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
                    $yeni_evrak->cikisGumruk = $formData[$i]["cıkısGumruk"];
                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    // Veterineri sistem limite göre atayacak
                    $veteriner = $this->atamaServisi->assignVet('antrepo_cikis');

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
                    $saved_count++; // Başarıyla eklenen evrak sayısını artır
                }
            }



            return redirect()->route('admin.evrak.index')->with('success', "$saved_count evrak başarıyla eklendi.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
    }

    public function edit($type, $evrak_id)
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
            $data['evrak'] = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
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
        } elseif ($request->type == "EvrakAntrepoGiris") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
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
                'USKSSertifikaReferansNo' => 'required',
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
                'vetSaglikSertifikasiNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
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
                $evrak->USKSSertifikaReferansNo = $request->USKSSertifikaReferansNo;
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
    }
}
