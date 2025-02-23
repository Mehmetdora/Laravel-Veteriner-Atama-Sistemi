<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Izin;
use App\Models\User;
use Exception;
use function Termwind\parse;

use Illuminate\Http\Request;
use function PHPSTORM_META\map;
use Illuminate\Support\Facades\Validator;

class IzinController extends Controller
{

    public function create()
    {

        $data['vets'] = User::role('veteriner')->where('status', 1)->get();
        $data['izins'] = Izin::all();

        return view('admin.izins.create', $data);
    }

    public function created(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'vet_id' => 'required',
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
            $dateStart = DateTime::createFromFormat('d/m/Y', $start)->format('Y-m-d');
            $dateEnd = DateTime::createFromFormat('d/m/Y', $end)->format('Y-m-d');


            $izin = null;
            $izinler = Izin::all()->pluck('name')->toArray();
            if (!(in_array($request->izin_name, $izinler))) {  // yeni geleni kaydet, değilse bul
                $izin = Izin::create(['name' => $request->izin_name]);
            } else {
                $izin = Izin::where('name', $request->izin_name)->first();
            };


            User::find($request->vet_id)
                ->izins()
                ->attach($izin->id, ['startDate' => $dateStart, 'endDate' => $dateEnd]);

            return redirect()->route('admin.izin.index')->with('success', 'Veteriner izni başarıyla eklendi!');
        } catch (Exception $errors) {
            return redirect()->route('admin.izin.index')->with('error', 'Veteriner izni eklenirken hata oluştu:' . $errors . ', lütfen bilgileri kontrol edip tekrar deneyiniz!');
        }
    }


    public function index()
    {

        //dd(User::first()->izins[0]);

        $data['vets'] = User::role('veteriner')->where('status', 1)->with('izins')->get();
        // veterinerleri izinleri ile birlikte gönder
        // view da userları dön, her user ın izinleri varsa dön
        // her izin için bir takvim öğesi oluştur
        // silme butonuna tıklanınca izinin id sini gönder ve anında silme işlemi yap
        // ekleme işlmelerini başka bir sayfada yap

        return view('admin.izins.index', $data);
    }


    public function delete(Request $request) {

        try {

            $vet = User::find($request->user_id);

            $vet->izins()->wherePivot('startDate',$request->start_date)
            ->wherePivot('endDate',$request->end_date)
            ->detach($request->izin_id);

            return response()->json(['success' => true, 'message' => 'Veteriner izini başarıyla silinmiştir!']);
        } catch (Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }

    }
}
