<?php

namespace App\Http\Controllers\admin\izin;

use DateTime;
use Exception;
use App\Models\Izin;
use App\Models\User;
use App\Models\Telafi;
use function Termwind\parse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use function PHPSTORM_META\map;

use App\Http\Controllers\Controller;
use App\Providers\BetaDegeriBulma;
use Illuminate\Support\Facades\Validator;
use App\Providers\OrtalamaGunlukWorkloadDegeriBulma;
use PHPUnit\Framework\MockObject\Generator\TemplateLoader;

class VeterinerIzinController extends Controller
{

    protected $ortalama_gunluk_workload_degeri_bulma;
    protected $beta_degeri_bulma;

    function __construct(BetaDegeriBulma $beta_degeri_bulma ,OrtalamaGunlukWorkloadDegeriBulma $ortalama_gunluk_workload_degeri_bulma)
    {
        $this->beta_degeri_bulma = $beta_degeri_bulma;
        $this->ortalama_gunluk_workload_degeri_bulma = $ortalama_gunluk_workload_degeri_bulma;
    }
    public function create()
    {

        $data['vets'] = User::role('veteriner')->where('status', 1)->get();
        $data['izins'] = Izin::all();

        return view('admin.izins.veteriners.create', $data);
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
            $dateStart = DateTime::createFromFormat('d/m/Y H:i A', $start)->format('Y-m-d H:i:s');
            $dateEnd = DateTime::createFromFormat('d/m/Y H:i A', $end)->format('Y-m-d H:i:s');


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






            // *****************************************

            //İzin-Telafi Katsayısı
            $izin_telafi_katsayisi = 6;

            $startDate = Carbon::createFromFormat('d/m/Y H:i A', $start);
            $endDate = Carbon::createFromFormat('d/m/Y H:i A', $end);

            // Başlangıç tarihini dahil ederek gün farkını hesaplama
            $izin_suresi = (int)($startDate->diffInDays($endDate)) + 1;
            $telafi_suresi = $izin_suresi * $izin_telafi_katsayisi;


            $user = User::find($request->vet_id);
            $user_workload = $user->veterinerinBuYilkiWorkloadi();

            $gunluk_ortalama_gelen_workload = $this->ortalama_gunluk_workload_degeri_bulma->ortalamaWorkloadHesapla();

            $total_telafi = $izin_suresi * $gunluk_ortalama_gelen_workload;
            $gunluk_telafi = (int)($total_telafi / $telafi_suresi);





            for ($i = 0; $i < $telafi_suresi; $i++) {

                if ($i == 0) {

                    $startDate = \Carbon\Carbon::parse($dateEnd);
                    $newDate = (clone $startDate)->addDay();

                    $telafi = new Telafi;
                    $telafi->izin_id = $izin->id;
                    $telafi->workload_id = $user_workload->id;
                    $telafi->tarih = $newDate->format('Y-m-d');
                    $telafi->total_telafi_workload = $gunluk_telafi;
                    $telafi->remaining_telafi_workload = $gunluk_telafi;
                    $telafi->save();
                } else {

                    $startDate = \Carbon\Carbon::parse($dateEnd);
                    $newDate = (clone $startDate)->addDays($i + 1); // izinden sonraki günler olduğu için +1
                    $newDate = $newDate->format('Y-m-d');

                    $telafi = new Telafi;
                    $telafi->izin_id = $izin->id;
                    $telafi->workload_id = $user_workload->id;
                    $telafi->tarih = $newDate;
                    $telafi->total_telafi_workload = $gunluk_telafi;
                    $telafi->remaining_telafi_workload = $gunluk_telafi;
                    $telafi->save();
                }
            }

            // *****************************************




            return redirect()->route('admin.izin.veteriner.index')->with('success', 'Veteriner izni başarıyla eklendi!');
        } catch (Exception $errors) {
            return redirect()->route('admin.izin.veteriner.index')->with('error', 'Veteriner izni eklenirken hata oluştu:' . $errors . ', lütfen bilgileri kontrol edip tekrar deneyiniz!');
        }
    }


    public function index()
    {

        $data['vets'] = User::role('veteriner')->where('status', 1)->with('izins')->get();
        // veterinerleri izinleri ile birlikte gönder
        // view da userları dön, her user ın izinleri varsa dön
        // her izin için bir takvim öğesi oluştur
        // silme butonuna tıklanınca izinin id sini gönder ve anında silme işlemi yap
        // ekleme işlmelerini başka bir sayfada yap

        return view('admin.izins.veteriners.index', $data);
    }


    public function delete(Request $request)
    {

        try {

            $vet = User::find($request->user_id);

            $vet->izins()->wherePivot('startDate', $request->start_date)
                ->wherePivot('endDate', $request->end_date)
                ->detach($request->izin_id);

            return response()->json(['success' => true, 'message' => 'Veteriner izini başarıyla silinmiştir!']);
        } catch (Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }
}
