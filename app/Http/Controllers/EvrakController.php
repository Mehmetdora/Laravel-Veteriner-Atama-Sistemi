<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Evrak;
use App\Models\EvrakTur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvrakController extends Controller
{
    public function index(){

        $evraklar = Evrak::with('veteriner')->get()->all();
        $data['evraklar'] = $evraklar;

        return view('admin.evrak_kayit.index',$data);
    }

    public function detail($evrak_id){

        $data['evrak'] = Evrak::find($evrak_id);
        return view('admin.evrak_kayit.detail',$data);
    }

    public function edit($evrak_id){

        $data['veteriners'] = User::role('veteriner')->get();
        $data['evrak_turs'] = EvrakTur::where('status',true)->get();
        $data['evrak'] = Evrak::find($evrak_id);

        return view('admin.evrak_kayit.edit',$data);
    }

    public function edited(Request $request){
        $validator = Validator::make($request->all(), [
            'siraNo' => 'required',
            'vgbOnBildirimNo' => 'required',
            'ithalatTür' => 'required',
            'vetSaglikSertifikasiNo' => 'required',
            'vekaletFirmaKisiId' => 'required',
            'urunAdi' => 'required',
            'kategoriId' => 'required',
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
            return redirect()->back()->with('error',$errors);
        }

        $evrak = Evrak::find($request->id);

        $evrak->siraNo = $request->siraNo;
        $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
        $evrak->ithalatTür = $request->ithalatTür;
        $evrak->vetSaglikSertifikasiNo = $request->vetSaglikSertifikasiNo;
        $evrak->vekaletFirmaKisiId = $request->vekaletFirmaKisiId;
        $evrak->urunAdi = $request->urunAdi;
        $evrak->kategoriId = $request->kategoriId;
        $evrak->gtipNo = $request->gtipNo;
        $evrak->urunKG = $request->urunKG;
        $evrak->sevkUlke = $request->sevkUlke;
        $evrak->orjinUlke = $request->orjinUlke;
        $evrak->aracPlaka = $request->aracPlaka;
        $evrak->girisGumruk = $request->girisGumruk;
        $evrak->cıkısGumruk = $request->cıkısGumruk;
        $evrak->tarih = Carbon::now();

        $veteriner = User::find($request->veterinerId);
        $saved = $veteriner->evraks()->save($evrak);

        if($saved){
            return redirect()->route('admin.evrak.index')->with('success','Evrak Başarıyla Düzenlendi.');
        }else{
            return redirect()->back()->with('error','Evrak Düzenleme Sırasında Hata Oluştu! Lütfen Bilgilerinizi Kontrol Ediniz.');
        }
    }


    public function create(){

        $data['evrak_turs'] = EvrakTur::where('status',true)->get();
        return view('admin.evrak_kayit.create',$data);
    }

    public function created(Request $request){

        $validator = Validator::make($request->all(), [
            'siraNo' => 'required',
            'vgbOnBildirimNo' => 'required',
            'ithalatTür' => 'required',
            'vetSaglikSertifikasiNo' => 'required',
            'vekaletFirmaKisiId' => 'required',
            'urunAdi' => 'required',
            'kategoriId' => 'required',
            'gtipNo' => 'required',
            'urunKG' => 'required',
            'sevkUlke' => 'required',
            'orjinUlke' => 'required',
            'aracPlaka' => 'required',
            'girisGumruk' => 'required',
            'cıkısGumruk' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error',$errors);
        }


        $yeni_evrak = new Evrak;

        $yeni_evrak->siraNo = $request->siraNo;
        $yeni_evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
        $yeni_evrak->ithalatTür = $request->ithalatTür;
        $yeni_evrak->vetSaglikSertifikasiNo = $request->vetSaglikSertifikasiNo;
        $yeni_evrak->vekaletFirmaKisiId = $request->vekaletFirmaKisiId;
        $yeni_evrak->urunAdi = $request->urunAdi;
        $yeni_evrak->kategoriId = $request->kategoriId;
        $yeni_evrak->gtipNo = $request->gtipNo;
        $yeni_evrak->urunKG = $request->urunKG;
        $yeni_evrak->sevkUlke = $request->sevkUlke;
        $yeni_evrak->orjinUlke = $request->orjinUlke;
        $yeni_evrak->aracPlaka = $request->aracPlaka;
        $yeni_evrak->girisGumruk = $request->girisGumruk;
        $yeni_evrak->cıkısGumruk = $request->cıkısGumruk;
        $yeni_evrak->tarih = Carbon::now();

        $veteriner = User::with('evraks')->role('veteriner')->first();   // bunu sistem belirleyecek
        $saved = $veteriner->evraks()->save($yeni_evrak);


        if($saved){
            return redirect()->route('admin.evrak.index')->with('success','Evrak Başarıyla Eklendi.');
        }else{
            return redirect()->back()->with('error','Evrak Kaydı Sırasında Hata Oluştu! Lütfen Bilgilerinizi Kontrol Ediniz.');
        }
    }
}
