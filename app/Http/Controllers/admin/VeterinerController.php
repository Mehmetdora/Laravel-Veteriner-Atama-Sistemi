<?php

namespace App\Http\Controllers\admin;

use App\Models\EvrakDurum;
use App\Models\User;
use App\Models\Evrak;
use App\Models\EvrakTur;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VeterinerController extends Controller
{

    public function index(){


        $veterinerler = User::role('veteriner')->get();
        $data['veterinerler'] = $veterinerler;

        return view('admin.veteriners.index',$data);
    }

    public function create(){
        return view('admin.veteriners.create');
    }

    public function created(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|max:10|min:10|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error',$errors);
        }

        $vet = new User;
        $vet->name= $request->name;
        $vet->username= $request->username;
        $vet->email= $request->email;
        $vet->password= bcrypt($request->password);
        $vet->phone_number= $request->phone_number;
        $vet->assignRole('veteriner');

        $vet->save();

        return redirect()->route('admin.veteriners.index')->with('success','Veteriner Başarıyla Ekledi!');

    }

    public function evraks_list($id){
        $data['veteriner'] = User::with('evraks')->find($id);

        return view('admin.veteriners.veteriner.evraks.index',$data);
    }

    public function edit($id){
        $data['veteriner'] = User::with('evraks')->find($id);
        return view('admin.veteriners.veteriner.edit',$data);
    }

    public function edited(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|max:10|min:10|unique:users',
            'password' => 'Nullable|min:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error',$errors);
        }

        $vet = User::find($request->input('id'));
        $vet->name = $request->name;
        $vet->username = $request->username;
        $vet->email = $request->email;
        $vet->phone_number = $request->phone_number;
        if($request->input('password')){
            $vet->password = $request->password;
        }
        $save = $vet->save();

        if($save){
            return redirect()->route('admin.veteriners.index')->with('success','Veteriner Bilgileri Başarıyla Güncellendi!');
        }else{
            return redirect()->back()->with('error','Lütfen Bilgileri Kontrol Ederek Tekrar Deneyiniz!');
        }


    }


    public function evrak_edit($id){

        $data['evrak'] = Evrak::find($id);
        $data['veteriners'] = User::role('veteriner')->get();
        $data['evrak_turs'] = EvrakTur::where('status',true)->get();

        return view('admin.veteriners.veteriner.evraks.edit',$data);


    }

    public function evrak_edited(Request $request){

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


        $evrak->evrak_durumu()->delete();
        $evrak_durum = new EvrakDurum;
        $evrak_durum->evrak_durum = $request->evrak_durum;
        $evrak->evrak_durumu()->save($evrak_durum);


        if($saved){
            return redirect()->route('admin.veteriners.veteriner.evraks',$veteriner->id)->with('success','Evrak Başarıyla Düzenlendi.');
        }else{
            return redirect()->back()->with('error','Evrak Düzenleme Sırasında Hata Oluştu! Lütfen Bilgilerinizi Kontrol Ediniz.');
        }

    }



    public function delete($id){

        $veteriner = User::find($id);

    }
}
