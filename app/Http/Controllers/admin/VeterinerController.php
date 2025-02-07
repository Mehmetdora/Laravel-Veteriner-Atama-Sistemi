<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VeterinerController extends Controller
{

    public function index(){


        $veterinerler = User::role('veteriner')->get();
        $data['veterinerler'] = $veterinerler;

        return view('admin.veteriner.index',$data);
    }

    public function create(){
        return view('admin.veteriner.create');
    }

    public function created(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|max:10|min:10',
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

        return redirect()->route('admin.veteriner.index')->with('success','Veteriner Başarıyla Ekledi!');

    }
}
