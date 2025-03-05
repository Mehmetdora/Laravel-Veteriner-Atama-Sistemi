<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\User;
use App\Models\Evrak;
use App\Models\EvrakDurum;
use App\Models\EvrakTur;
use App\Models\Urun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvrakController extends Controller
{
    public function index()
    {

        $evraklar = Evrak::with(['veteriner', 'evrak_tur', 'urun'])->orderByDesc('created_at')->get();
        $data['evraklar'] = $evraklar;

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
        $data['evrak_turs'] = EvrakTur::where('status', true)->get();
        return view('admin.evrak_kayit.create', $data);
    }

    public function created(Request $request)
    {

        $formData = json_decode($request->formData, true); // JSON stringi diziye çeviriyoruz

        if (!$formData) {
            return redirect()->back()->with('error', 'Geçersiz veri formatı!');
        }


        $errors = [];

        foreach ($formData as $index => $form) {
            $validator = Validator::make($form, [
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
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->all();
            }
        }

        // Eğer hata varsa, geriye yönlendir ve tüm hataları göster
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->with('error', $errors);
        }


        try {
            $saved_count = 0; // Başarıyla kaydedilen evrak sayısı

            foreach ($formData as $form) {
                $yeni_evrak = new Evrak;

                $yeni_evrak->siraNo = $form["siraNo"];
                $yeni_evrak->vgbOnBildirimNo = $form["vgbOnBildirimNo"];
                $yeni_evrak->vetSaglikSertifikasiNo = "Model ile bağlanacak";
                $yeni_evrak->vekaletFirmaKisiAdi = $form["vekaletFirmaKisiAdi"];
                $yeni_evrak->urunAdi = $form["urunAdi"];
                $yeni_evrak->gtipNo = $form["gtipNo"];
                $yeni_evrak->urunKG = $form["urunKG"];
                $yeni_evrak->sevkUlke = $form["sevkUlke"];
                $yeni_evrak->orjinUlke = $form["orjinUlke"];
                $yeni_evrak->aracPlaka = $form["aracPlaka"];
                $yeni_evrak->girisGumruk = $form["girisGumruk"];
                $yeni_evrak->cıkısGumruk = $form["cıkısGumruk"];
                $yeni_evrak->tarih = Carbon::now();

                // İlişkili modelleri bağlama
                $urun = Urun::find($form["urun_kategori_id"]);
                $evrak_tur = EvrakTur::find($form["evrak_tur_id"]);
                $veteriner = User::with('evraks')->role('veteriner')->first();

                if (!$urun || !$evrak_tur || !$veteriner) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $yeni_evrak->urun()->associate($urun);
                $yeni_evrak->evrak_tur()->associate($evrak_tur);

                // Veteriner ile evrak kaydetme
                $saved = $veteriner->evraks()->save($yeni_evrak);
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = new EvrakDurum;
                $yeni_evrak->evrak_durumu()->save($evrak_durum);

                $saved_count++; // Başarıyla eklenen evrak sayısını artır
            }


            return redirect()->route('admin.evrak.index')->with('success', "$saved_count evrak başarıyla eklendi.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
    }
}
