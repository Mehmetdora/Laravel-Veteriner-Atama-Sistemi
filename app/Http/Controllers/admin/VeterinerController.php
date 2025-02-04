<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VeterinerController extends Controller
{
    public function index(){


        $veterinerler = User::role('veteriner')->get();
        $data['veterinerler'] = $veterinerler;

        return view('admin.veteriner.index',$data);
    }
}
