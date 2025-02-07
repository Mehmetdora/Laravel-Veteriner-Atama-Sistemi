<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class Authentication extends Controller
{
    public function login(){
        return view('Authentication.login');
    }

    public function logined(Request $request){

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Yanlış Şifre';
            return redirect()->back()->with('error',$errors);
        }

        if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){
            $user = Auth::user();

            if($user->hasRole('admin')){
                return redirect()->route('admin_dashboard');

            }else if ($user->hasRole('veteriner')){
                return redirect()->route('veteriner_dashboard');

            }else if ($user->hasRole('memur')){
                return redirect()->route('memur_dashboard');
            }

        }else{
            return redirect()->back()->with('error','Lütfen bilgilerinizi kontrol ediniz. Girdiğiniz bilgiler hatalıdır!');
        }

        return ;
    }



    public function logout(){
        if(Auth::check()){
            Auth::logout();
            return redirect()->route('login');
        }else{
            return redirect()->route('login');
        }
    }
}
