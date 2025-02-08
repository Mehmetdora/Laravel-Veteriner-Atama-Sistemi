<?php

namespace App\Http\Controllers\admin;

use App\Models\EvrakTur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class EvrakTurController extends Controller
{
    public function index(){

        $data['evrakTurs'] = EvrakTur::where('status',true)->get();
        return view('admin.evrak_tur.index',$data);
    }


    public function create(){
        return view('admin.evrak_tur.create');
    }
    public function created(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error',$errors);
        }

        $evrak_t = new EvrakTur;
        $evrak_t->name= $request->name;
        $evrak_t->save();

        return redirect()->route('admin.evrak_tur.index')->with('success','Evrak Türü Başarıyla Eklendi!');
    }

    public function edit($id){

        $data['evrak_t'] = EvrakTur::find($id);
        return view('admin.evrak_tur.edit',$data);
    }
    public function edited(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error',$errors);
        }

        $evrak_t = EvrakTur::find($request->evrak_t_id);
        $evrak_t->name= $request->name;
        $evrak_t->save();

        return redirect()->route('admin.evrak_tur.index')->with('success','Evrak Türü Başarıyla Güncellendi!');
    }

    public function delete($id){

        $evrakT = EvrakTur::find($id);
        $evrakT->status = false;
        $evrakT->save();

        return redirect()->route('admin.evrak_tur.index')->with('success','Evrak Türü Başarıyla Silindi!');
    }

}
