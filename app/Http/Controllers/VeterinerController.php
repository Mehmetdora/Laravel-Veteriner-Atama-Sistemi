<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VeterinerController extends Controller
{
    public function dashboard(){
        return view('veteriner.dashboard');
    }
}
