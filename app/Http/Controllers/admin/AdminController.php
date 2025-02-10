<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dashboard(){
        return view('admin.dashboard');
    }

    public function profile(){
        return view('admin.admin_profile.index');
    }

    public function edit(){
        return view('admin.admin_profile.edit');
    }
    public function edited(Request $request){
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
            }
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email ;
        $user->phone_number = $request->phone_number;

        $save = $user->save();

        if($save){
            return redirect()->route('admin_profile')->with('success','Yönetici Bilgileri Başarıyla Güncellendi!');
        }else{
            return redirect()->back()->with('error','Lütfen Bilgileri Kontrol Ederek Tekrar Doldurunuz!');
        }




    }
}
