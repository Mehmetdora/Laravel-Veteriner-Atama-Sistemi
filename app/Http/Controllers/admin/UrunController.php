<?php

namespace App\Http\Controllers\admin;

use App\Models\Urun;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UrunController extends Controller
{
    public function index(){

        $data['uruns'] = Urun::all();
        return view('admin.urun_kategori.index',$data);
    }


    public function create(){
        return view('admin.urun_kategori.create');
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

        $urun = new Urun;
        $urun->name= $request->name;
        $urun->save();

        return redirect()->route('admin.uruns.index')->with('success','Ürün Başarıyla Eklendi!');
    }

    public function edit($id){

        $data['urun'] = Urun::find($id);
        return view('admin.urun_kategori.edit',$data);
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

        $urun = Urun::find($request->urun_id);
        $urun->name= $request->name;
        $urun->save();

        return redirect()->route('admin.uruns.index')->with('success','Ürün Başarıyla Güncellendi!');
    }

    public function delete($id){

        $urun = Urun::find($id);
        $urun->delete();

        return redirect()->route('admin.uruns.index')->with('success','Ürün Başarıyla Silindi!');
    }
}
