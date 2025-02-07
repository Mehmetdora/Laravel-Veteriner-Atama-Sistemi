<?php

namespace App\Http\Controllers\memur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemurController extends Controller
{
    public function dashboard(){
        return view('memur.dashboard');
    }
}
