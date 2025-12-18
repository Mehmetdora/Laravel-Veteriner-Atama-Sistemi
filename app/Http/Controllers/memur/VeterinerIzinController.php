<?php

namespace App\Http\Controllers\memur;

use DateTime;
use Exception;
use App\Models\Izin;
use App\Models\User;
use App\Models\Telafi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Providers\BetaDegeriBulma;
use Illuminate\Support\Facades\Validator;
use App\Providers\OrtalamaGunlukWorkloadDegeriBulma;
use App\Providers\YearWorklaodOrtalamasınıBulma;

class VeterinerIzinController extends Controller
{
    protected $ortalama_gunluk_workload_degeri_bulma;
    protected $beta_degeri_bulma;
    protected $ortalama_year_worklaod_degeri_bulma;

    function __construct(YearWorklaodOrtalamasınıBulma $ortalama_year_worklaod_degeri_bulma, BetaDegeriBulma $beta_degeri_bulma, OrtalamaGunlukWorkloadDegeriBulma $ortalama_gunluk_workload_degeri_bulma)
    {
        $this->ortalama_year_worklaod_degeri_bulma = $ortalama_year_worklaod_degeri_bulma;
        $this->beta_degeri_bulma = $beta_degeri_bulma;
        $this->ortalama_gunluk_workload_degeri_bulma = $ortalama_gunluk_workload_degeri_bulma;
    }
    public function create()
    {

        $data['vets'] = User::role('veteriner')->where('status', 1)->get();
        $data['izins'] = Izin::all();

        return view('memur.izins.create', $data);
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
            $dateStart = DateTime::createFromFormat('d/m/Y H:i', $start)->format('Y-m-d H:i:s');
            $dateEnd = DateTime::createFromFormat('d/m/Y H:i', $end)->format('Y-m-d H:i:s');


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

            $startDate = Carbon::createFromFormat('d/m/Y H:i', $start);
            $endDate = Carbon::createFromFormat('d/m/Y H:i', $end);

            // Başlangıç tarihini dahil ederek gün farkını hesaplama
            $izin_suresi = (int)($startDate->diffInDays($endDate)) + 1;
            $telafi_suresi = $izin_suresi * $izin_telafi_katsayisi;


            $user = User::find($request->vet_id);
            $user_workload = $user->veterinerinBuYilkiWorkloadi();

            $gunluk_ortalama_gelen_workload = $this->ortalama_gunluk_workload_degeri_bulma->ortalamaWorkloadHesapla();

            $total_telafi = $izin_suresi * $gunluk_ortalama_gelen_workload;
            $gunluk_telafi = (int)($total_telafi / $telafi_suresi);



            $startDate = \Carbon\Carbon::parse($dateEnd)->addDay(); // İlk telafi tarihi, izinden sonraki gün
            $i = 0;
            while ($i < $telafi_suresi) {
                // Eğer gün Cumartesi veya Pazar ise hafta içi bir güne kadar artır
                // Böylece sadece hafta içi günleri için telafi oluşturulacak
                while ($startDate->isWeekend()) {
                    $startDate->addDay();
                }

                // Telafi kaydını oluştur
                $telafi = new Telafi;
                $telafi->izin_id = $izin->id;
                $telafi->workload_id = $user_workload->id;
                $telafi->tarih = $startDate->format('Y-m-d');
                $telafi->total_telafi_workload = $gunluk_telafi;
                $telafi->remaining_telafi_workload = $gunluk_telafi;
                $telafi->save();

                // Bir sonraki günü kontrol et
                $startDate->addDay();
                $i++;
            }


            // *****************************************



            /*
                Bu ortalama değer telafisi ve izni olamayan veterinerlerin year_worklaod değerlerinin
                ortalaması alınarak hesaplanmıştır.
            */
            $ortalama_year_worklaod = $this->ortalama_year_worklaod_degeri_bulma->DigerVetsOrtalamaYearWorklaodDegeri();
            $user_workload->temp_workload = $ortalama_year_worklaod;
            $user_workload->save();




            return redirect()->route('memur.izin.veteriner.index')->with('success', 'Veteriner izni başarıyla eklendi!');
        } catch (Exception $errors) {
            return redirect()->route('memur.izin.veteriner.index')->with('error', 'Veteriner izni eklenirken hata oluştu:' . $errors . ', lütfen bilgileri kontrol edip tekrar deneyiniz!');
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

        return view('memur.izins.index', $data);
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
