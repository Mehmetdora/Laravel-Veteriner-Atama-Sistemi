<?php

namespace App\Http\Controllers\veteriner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Evrak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VeterinerController extends Controller
{
    public function dashboard(){

        $vet = Auth::user();
        $data['unread_evraks_count'] = $vet->unread_evraks_count();

        return view('veteriner.dashboard',$data);
    }

    public function profile_index(){
        $data['unread_evraks_count'] = Auth::user()->unread_evraks_count();
        return view('veteriner.profile.index',$data);
    }

    public function profile_edit(){
        $data['unread_evraks_count'] = Auth::user()->unread_evraks_count();
        return view('veteriner.profile.edit',$data);
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
            return redirect()->route('veteriner.profile.index')->with('success','Kullanıcı Bilgileri Başarıyla Güncellendi!');
        }else{
            return redirect()->back()->with('error','Lütfen Bilgileri Kontrol Ederek Tekrar Doldurunuz!');
        }
    }

    public function evraks_index(){
        $vet = Auth::user();
        $evraks = $vet->evraks;

        foreach ($evraks as $evrak) {
            $evrak->evrak_durumu->update(['isRead'=>1]);
        }
        $data['evraklar'] = $evraks;


        return view('veteriner.evraks.index',$data);

    }

    public function evrak_index($id){
        $data['evrak'] = Evrak::find($id);
        $data['unread_evraks_count'] = Auth::user()->unread_evraks_count();

        return view('veteriner.evraks.evrak.index',$data);

    }

}
