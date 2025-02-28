<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MemurController extends Controller
{
    public function index()
    {
        date_default_timezone_set('Europe/Istanbul');
        $today = date('Y-m-d');

        // Tüm memurlar
        $memurlar = User::role('memur')
            ->with(['izins'])
            ->where("status", 1)
            ->get();

        // izinli memurları bulma
        $izinliler = [];
        foreach ($memurlar as $memur) {
            $is_izinli = $memur->izins()->wherePivot('startDate','<=',$today)->wherePivot('endDate','>=',$today)->get();
            if(!($is_izinli->isEmpty())){
                $izinliler[] = $memur->id;
            }
        }


        // VETERİNER BİLGİLERİNİN TEKRAR PAKETLENMESİ
        $memurlar = collect($memurlar)->map(function ($memur) use ($izinliler) {
            return [
                'id' => $memur->id,
                'name' => $memur->name,
                'is_izinli' => in_array($memur->id,$izinliler),
                'created_at' => $memur->created_at,
            ];
        });

        $data['memurlar'] = $memurlar;
        return view('admin.memurs.index', $data);
    }

    public function create()
    {
        return view('admin.memurs.create');
    }

    public function created(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('status', 1);
                }), // user tablosundaki değerlerden status ü 1 olanlar içinden unique kontrolü
            ],
            'phone_number' => [
                'required',
                'max:10',
                'min:10',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('status', 1);
                }),
            ],
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error', $errors)->withInput();
        }

        // EĞER SİLİNMİŞ BİR KULLANICI YENİDEN EKLENİLECEK OLURSA
        $user_old = User::where("phone_number", $request->phone_number)
            ->orWhere("email", $request->email)
            ->first();
        if (isset($user_old)) {
            $user_old->status = 1;
            $user_old->name = $request->name;
            $user_old->username = $request->username;
            $user_old->email = $request->email;
            $user_old->password = bcrypt($request->password);
            $user_old->phone_number = $request->phone_number;

            $user_old->save();

            return redirect()->route('admin.memurs.index')->with('success', 'Memur Başarıyla Ekledi!');
        }

        $vet = new User;
        $vet->name = $request->name;
        $vet->username = $request->username;
        $vet->email = $request->email;
        $vet->password = bcrypt($request->password);
        $vet->phone_number = $request->phone_number;
        $vet->assignRole('memur');

        $vet->save();

        return redirect()->route('admin.memurs.index')->with('success', 'Memur Başarıyla Ekledi!');
    }

    public function edit($id)
    {
        $data['memur'] = User::find($id);
        return view('admin.memurs.edit', $data);
    }

    public function edited(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('id', '!=', $request->id);
                    // users arasından gelen id ye sahip kullanıcı hariç diğer kullanıcılar arasına unique phone num.
                }),
            ],
            'phone_number' => [
                'required',
                'max:10',
                'min:10',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('id', '!=', $request->id);
                    // users arasından gelen id ye sahip kullanıcı hariç diğer kullanıcılar arasına unique phone num.
                }),
            ],
            'password' => 'Nullable|min:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error', $errors);
        }

        $vet = User::find($request->input('id'));
        $vet->name = $request->name;
        $vet->username = $request->username;
        $vet->email = $request->email;
        $vet->phone_number = $request->phone_number;
        if ($request->input('password')) {
            $vet->password = $request->password;
        }
        $save = $vet->save();

        if ($save) {
            return redirect()->route('admin.memurs.index')->with('success', 'Memur Bilgileri Başarıyla Güncellendi!');
        } else {
            return redirect()->back()->with('error', 'Lütfen Bilgileri Kontrol Ederek Tekrar Deneyiniz!');
        }
    }


    public function delete($id)
    {

        $veteriner = User::find($id);
        $veteriner->status = 0;

        $veteriner->save();
        return redirect()->route('admin.memurs.index')->with('success', 'Memur Başarıyla Sistemden Çıkarılmıştır!');
    }
}
