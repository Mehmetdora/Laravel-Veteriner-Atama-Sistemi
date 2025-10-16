<?php

namespace App\Http\Controllers\admin\izin;

use DateTime;
use Exception;
use App\Models\Izin;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MemurIzinController extends Controller
{
    public function create()
    {


        $data['memurs'] = User::role('memur')->where('status', 1)->get();
        $data['izins'] = Izin::all();

        return view('admin.izins.memurs.create', $data);
    }

    public function created(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'memur_id' => 'required',
            'izin_name' => 'required',
            'izin_tarihleri' => 'required',

        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error', $errors)->withInput();
        }





        try {
            [$start, $end] = explode(' - ', $request->izin_tarihleri); // split the string into start and end dates
            // create DateTime objects from the strings
            $dateStart = DateTime::createFromFormat('d/m/Y H:i', $start)->format('Y-m-d H:i:s');
            $dateEnd = DateTime::createFromFormat('d/m/Y H:i', $end)->format('Y-m-d H:i:s');


            $izin = null;
            $izinler = Izin::all()->pluck('name')->toArray();
            if (!(in_array($request->izin_name, $izinler))) {  // yeni geleni kaydet, değilse bul
                $izin = Izin::create(['name' => $request->izin_name]);
            } else {
                $izin = Izin::where('name', $request->izin_name)->first();
            };


            User::find($request->memur_id)
                ->izins()
                ->attach($izin->id, ['startDate' => $dateStart, 'endDate' => $dateEnd]);

            return redirect()->route('admin.izin.memur.index')->with('success', 'Memur izni başarıyla eklendi!');
        } catch (Exception $errors) {
            return redirect()->route('admin.izin.memur.index')->with('error', 'Memur izni eklenirken hata oluştu:' . $errors . ', lütfen bilgileri kontrol edip tekrar deneyiniz!');
        }
    }


    public function index()
    {

        $data['memurs'] = User::role('memur')->where('status', 1)->with(['izins'])->get();
        // veterinerleri izinleri ile birlikte gönder
        // view da userları dön, her user ın izinleri varsa dön
        // her izin için bir takvim öğesi oluştur
        // silme butonuna tıklanınca izinin id sini gönder ve anında silme işlemi yap
        // ekleme işlmelerini başka bir sayfada yap

        return view('admin.izins.memurs.index', $data);
    }


    public function delete(Request $request)
    {

        try {

            $vet = User::find($request->user_id);

            $vet->izins()->wherePivot('startDate', $request->start_date)
                ->wherePivot('endDate', $request->end_date)
                ->detach($request->izin_id);

            return response()->json(['success' => true, 'message' => 'Memur izini başarıyla silinmiştir!']);
        } catch (Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }
}
