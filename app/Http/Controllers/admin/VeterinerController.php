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

        return redirect()->route('admin.veteriners.index')->with('success','Veteriner Başarıyla Ekledi!');

    }

    public function evraks_list($id){
        $data['veteriner'] = User::with('evraks')->find($id);

        return view('admin.veteriners.veteriner.evraks',$data);
    }

    public function edit($id){
        $data['veteriner'] = User::with('evraks')->find($id);
        return view('admin.veteriners.veteriner.edit',$data);
    }

    public function edited(){

    }

    public function delete($id){

        $veteriner = User::find($id);

    }
}
