<?php

namespace App\Http\Controllers;


use App\Models\SaglikSertifika;
use App\Models\UserEvrak;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Evrak;
use App\Models\EvrakAntrepoCikis;
use App\Models\EvrakAntrepoGiris;
use App\Models\EvrakAntrepoSertifika;
use App\Models\EvrakAntrepoVaris;
use App\Models\EvrakDurum;
use App\Models\EvrakIthalat;
use App\Models\EvrakTransit;
use App\Models\EvrakTur;
use App\Models\Urun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvrakController extends Controller
{
    public function index()
    {

        $evraks_all = collect()
            ->merge(EvrakIthalat::with(['veteriner.user', 'urun'])->get())
            ->merge(EvrakTransit::with(['veteriner.user', 'urun'])->get())
            ->merge(EvrakAntrepoGiris::with(['veteriner.user', 'urun'])->get())
            ->merge(EvrakAntrepoVaris::with(['veteriner.user', 'urun'])->get())
            ->merge(EvrakAntrepoSertifika::with(['veteriner.user', 'urun'])->get())
            ->merge(EvrakAntrepoCikis::with(['veteriner.user', 'urun'])->get());

        // `created_at`'e göre azalan sırayla sıralama
        $evraks_all = $evraks_all->sortByDesc('created_at');
        $data['evraks_all'] = $evraks_all;

        return view('admin.evrak_kayit.index', $data);
    }

    public function detail($evrak_id)
    {

        $data['evrak'] = Evrak::with(['urun', 'veteriner', 'evrak_tur', 'evrak_durumu'])
            ->find($evrak_id);

        return view('admin.evrak_kayit.detail', $data);
    }

    public function edit($evrak_id)
    {

        $data['veteriners'] = User::role('veteriner')->where('status', 1)->get();
        $data['uruns'] = Urun::all();
        $data['evrak_turs'] = EvrakTur::where('status', true)->get();
        $data['evrak'] = Evrak::with(['urun', 'evrak_tur', 'veteriner'])->find($evrak_id);

        return view('admin.evrak_kayit.edit', $data);
    }

    public function edited(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'siraNo' => 'required',
            'vgbOnBildirimNo' => 'required',
            'evrak_tur_id' => 'required',
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
            'veterinerId' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error', $errors);
        }

        $evrak = Evrak::find($request->id);

        $evrak->siraNo = $request->siraNo;
        $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
        $evrak->vetSaglikSertifikasiNo = $request->vetSaglikSertifikasiNo;
        $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
        $evrak->urunAdi = $request->urunAdi;
        $evrak->gtipNo = $request->gtipNo;
        $evrak->urunKG = $request->urunKG;
        $evrak->sevkUlke = $request->sevkUlke;
        $evrak->orjinUlke = $request->orjinUlke;
        $evrak->aracPlaka = $request->aracPlaka;
        $evrak->girisGumruk = $request->girisGumruk;
        $evrak->cıkısGumruk = $request->cıkısGumruk;
        $evrak->tarih = Carbon::now();

        $urun = Urun::find($request->urun_kategori_id);
        $evrak->urun()->associate($urun);

        $evrak_tur = EvrakTur::find($request->evrak_tur_id);
        $evrak->evrak_tur()->associate($evrak_tur);

        $veteriner = User::find($request->veterinerId);
        $saved = $veteriner->evraks()->save($evrak);

        $isRead = $evrak->evrak_durumu->isRead;
        $evrak->evrak_durumu()->delete();

        $evrak_durum = new EvrakDurum;  // Evrak Durumu güncelleme
        $evrak_durum->isRead = $isRead;
        $evrak_durum->evrak_durum = $request->evrak_durum;
        $evrak->evrak_durumu()->save($evrak_durum);

        if ($saved) {
            return redirect()->route('admin.evrak.index')->with('success', 'Evrak Başarıyla Düzenlendi.');
        } else {
            return redirect()->back()->with('error', 'Evrak Düzenleme Sırasında Hata Oluştu! Lütfen Bilgilerinizi Kontrol Ediniz.');
        }
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

        $formData = json_decode($request->formData, true); // JSON stringi diziye çeviriyoruz

        if (!$formData) {
            return redirect()->back()->with('error', 'Geçersiz veri formatı!');
        }


        // İlk gelen formdaki evrağın türü ne ise diğerleride aynı türde olduğunu
        // varsayarak evrak türünü belirleyip tüm evrakları for ile özel validate işlemi uygulandı
        $errors = [];

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
                    'girisAntreposu' => 'required',
                    'varisAntreposu' => 'required',
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


        // dd($formData);

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
                    $veteriner = User::with('evraks')->role('veteriner')->first();

                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak->urun()->attach($urun->id);

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
                    $veteriner = User::with('evraks')->role('veteriner')->first();

                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak->urun()->attach($urun->id);

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
                    $veteriner = User::with('evraks')->role('veteriner')->first();

                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak->urun()->attach($urun->id);

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
                    $yeni_evrak->girisAntreposu = $formData[$i]["girisAntreposu"];
                    $yeni_evrak->varisAntreposu = $formData[$i]["varisAntreposu"];
                    $yeni_evrak->save();

                    // Veterineri sistem limite göre atayacak
                    $veteriner = User::with('evraks')->role('veteriner')->first();

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
                    $veteriner = User::with('evraks')->role('veteriner')->first();

                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak->urun()->attach($urun->id);

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
                    $yeni_evrak->girisGumruk = $formData[$i]["girisGumruk"];
                    $yeni_evrak->cikisGumruk = $formData[$i]["cıkısGumruk"];
                    $yeni_evrak->save();

                    // İlişkili modelleri bağlama
                    $urun = Urun::find($formData[$i]["urun_kategori_id"]);

                    // Veterineri sistem limite göre atayacak
                    $veteriner = User::with('evraks')->role('veteriner')->first();

                    if (!$urun || !$veteriner) {
                        throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                    }

                    $yeni_evrak->urun()->attach($urun->id);

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
}
