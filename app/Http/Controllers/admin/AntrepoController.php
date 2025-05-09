<?php

namespace App\Http\Controllers\admin;

use App\Models\GirisAntrepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AntrepoController extends Controller
{


    // Antrepoların Listesi
    public function index()
    {
        $antrepos = GirisAntrepo::actives();

        $data['antrepos'] = $antrepos;
        return view('admin.antrepos.index', $data);
    }

    public function create()
    {
        return view('admin.antrepos.create');
    }
    public function created(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error', $errors);
        }

        $antrepo = new GirisAntrepo;
        $antrepo->name = $request->name;
        $antrepo->save();

        return redirect()->route('admin.antrepos.index')->with('success', 'Antrepo Başarıyla Eklendi!');
    }
    public function edit($id)
    {
        $data['antrepo'] = GirisAntrepo::find($id);
        return view('admin.antrepos.edit', $data);
    }
    public function edited(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error', $errors);
        }

        $antrepo = GirisAntrepo::find($request->antrepo_id);
        $antrepo->name = $request->name;
        $antrepo->save();

        return redirect()->route('admin.antrepos.index')->with('success', 'Antrepo Başarıyla Güncellendi!');
    }
    public function delete($id) {
        $antrepo = GirisAntrepo::find($id);
        $antrepo->is_active = false;
        $antrepo->save();

        return redirect()->route('admin.antrepos.index')->with('success','Antrepo Başarıyla Silindi!');

    }
}
