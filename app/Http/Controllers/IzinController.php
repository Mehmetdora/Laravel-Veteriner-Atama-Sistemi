<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function index(){

        //dd(User::first()->izins[0]->pivot->startDate);

        $data['izins'] = User::role('veteriner')->where('status',1)->with('izins')->get();
        // veterinerleri izinleri ile birlikte gönder
        // view da userları dön, her user ın izinleri varsa dön
        // her izin için bir takvim öğesi oluştur
        // silme butonuna tıklanınca izinin id sini gönder ve anında silme işlemi yap
        // ekleme işlmelerini başka bir sayfada yap
        
        return view('admin.izins.index');
    }


    public function edited(Request $request){




    }
}
