<?php

namespace App\Http\Controllers\veteriner;

use App\Models\User;
use App\Models\Evrak;
use App\Models\UsksNo;
use App\Models\NobetHafta;
use App\Models\EvrakIthalat;
use App\Models\EvrakTransit;
use Illuminate\Http\Request;
use App\Models\EvrakCanliHayvan;
use App\Models\EvrakAntrepoCikis;
use App\Models\EvrakAntrepoGiris;
use App\Models\EvrakAntrepoVaris;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\EvrakAntrepoSertifika;
use Illuminate\Support\Facades\Validator;

class VeterinerController extends Controller
{
    public function dashboard()
    {

        $vet = Auth::user();
        $data['unread_evraks_count'] = $vet->unread_evraks_count();

        return view('veteriner.dashboard', $data);
    }

    public function profile_index()
    {
        $data['unread_evraks_count'] = Auth::user()->unread_evraks_count();
        return view('veteriner.profile.index', $data);
    }

    public function profile_edit()
    {
        $data['unread_evraks_count'] = Auth::user()->unread_evraks_count();
        return view('veteriner.profile.edit', $data);
    }

    public function profile_edited(Request $request)
    {

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
            return redirect()->back()->with('error', $errors);
        }

        $user = Auth::user();

        if (isset($request->password_old, $request->password)) {
            if (Hash::check($request->password_old, $user->password)) {
                $user->password = Hash::make($request->password);
            } else {
                return redirect()->back()->with('error', 'Mevcut şifreniz doğru değil, lütfen tekrar deneyiniz!');
            }
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        $save = $user->save();

        if ($save) {
            return redirect()->route('veteriner.profile.index')->with('success', 'Kullanıcı Bilgileri Başarıyla Güncellendi!');
        } else {
            return redirect()->back()->with('error', 'Lütfen Bilgileri Kontrol Ederek Tekrar Doldurunuz!');
        }
    }

    public function evraks_index()
    {
        $vet = User::with(['evraks' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'evraks.evrak.evrak_durumu'])
            ->find(Auth::id());
        $kayitlar = $vet->evraks;

        foreach ($kayitlar as $kayit) {
            $kayit->evrak->evrak_durumu->update(['isRead' => 1]);
        }
        $data['kayitlar'] = $kayitlar;


        return view('veteriner.evraks.index', $data);
    }

    public function evrak_index($type, $evrak_id)
    {

        $type = explode("\\", $type);
        $type = end($type);

        if ($type == "EvrakIthalat") {
            $data['evrak'] = EvrakIthalat::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakTransit") {
            $data['evrak'] = EvrakTransit::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoGiris") {
            $data['evrak'] = EvrakAntrepoGiris::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoVaris") {
            $data['evrak'] = EvrakAntrepoVaris::with(['veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoSertifika") {
            $data['evrak'] = EvrakAntrepoSertifika::with(['urun', 'veteriner.user',  'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoCikis") {
            $evrak = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
            $data['evrak'] = $evrak;
            $data['usks'] = UsksNo::find($evrak->usks_id);
        } else if ($type == "EvrakCanliHayvan") {
            $data['evrak'] = EvrakCanliHayvan::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        }

        $data['type'] = $type;


        $data['unread_evraks_count'] = Auth::user()->unread_evraks_count();

        return view('veteriner.evraks.evrak.index', $data);
    }


    public function nobets_index()
    {

        $vet = Auth::user();
        $data['nobets'] = $vet->nobets;
        $data['unread_evraks_count'] = Auth::user()->unread_evraks_count();

        return view('veteriner.nobets_index', $data);
    }

    public function izins_index()
    {

        $vet = Auth::user();
        $data['izins'] = $vet->izins;
        $data['unread_evraks_count'] = Auth::user()->unread_evraks_count();

        return view('veteriner.izins_index', $data);
    }

    public function onaylandi(Request $request)
    {
        try {
            $type = explode("\\", $request->evrak_type);
            $type = end($type);
            $id = (int)$request->evrak_id;

            if ($type == "EvrakIthalat") {
                $evrak = EvrakIthalat::with('evrak_durumu')
                    ->find($id);
            } else if ($type == "EvrakTransit") {
                $evrak = EvrakTransit::with('evrak_durumu')
                    ->find($id);
            } else if ($type == "EvrakAntrepoGiris") {
                $evrak = EvrakAntrepoGiris::with('evrak_durumu')
                    ->find($id);
            } else if ($type == "EvrakAntrepoVaris") {
                $evrak = EvrakAntrepoVaris::with('evrak_durumu')
                    ->find($id);
            } else if ($type == "EvrakAntrepoSertifika") {
                $evrak = EvrakAntrepoSertifika::with('evrak_durumu')
                    ->find($id);
            } else if ($type == "EvrakAntrepoCikis") {
                $evrak = EvrakAntrepoCikis::with('evrak_durumu')
                    ->find($id);
            } else if ($type == "EvrakCanliHayvan") {
                $evrak = EvrakCanliHayvan::with('evrak_durumu')
                    ->find($id);
            }else{
                $evrak = "bulunamadı";
            }


            $evrak->evrak_durumu->evrak_durum = $request->evrak_durum;
            $evrak->evrak_durumu->save();

            return response()->json(['success' => true, 'message' => 'Evrak Durumu Başarıyla Güncellendi!']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }
}
