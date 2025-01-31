<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class Authentication extends Controller
{
    public function login(){
        /* User::create([
            'name' => 'Veteriner ',
            'username' => 'veteriner123',
            'email' => 'veteriner@gmail.com',
            'phone_number' => '1231231212',
            'password' => bcrypt('123123')
        ])->assignRole('veteriner');
        User::create([
            'name' => 'admin ',
            'username' => 'admin123',
            'email' => 'admin@gmail.com',
            'phone_number' => '1231232323',
            'password' => bcrypt('123123')
        ])->assignRole('admin'); */

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
        }
    }
}
