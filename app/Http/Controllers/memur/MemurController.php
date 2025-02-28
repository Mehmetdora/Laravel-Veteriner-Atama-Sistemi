<?php

namespace App\Http\Controllers\memur;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MemurController extends Controller
{
    public function dashboard(){

        $data['vet'] = Auth::user();
        return view('memur.dashboard',$data);
    }

    public function profile_index(){
        return view('memur.profile.index');
    }

    public function profile_edit(){
        return view('memur.profile.edit');
    }

    public function profile_edited(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|max:10|min:10',
            'password_old' => 'Nullable',
            'password' => 'Nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error',$errors);
        }

        $user = Auth::user();

        if(isset($request->password_old,$request->password) ){
            if(Hash::check($request->password_old,$user->password)){
                $user->password = Hash::make($request->password);
            }else{
                return redirect()->back()->with('error','Mevcut şifreniz doğru değil, lütfen tekrar deneyiniz!');
            }
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email ;
        $user->phone_number = $request->phone_number;

        $save = $user->save();

        if($save){
            return redirect()->route('memur.profile.index')->with('success','Kullanıcı Bilgileri Başarıyla Güncellendi!');
        }else{
            return redirect()->back()->with('error','Lütfen Bilgileri Kontrol Ederek Tekrar Doldurunuz!');
        }
    }


    public function izins_index(){

        $izinler = Auth::user()->izins;
        $data['izins'] = $izinler;
        return view('memur.izins_index',$data);
    }
}

