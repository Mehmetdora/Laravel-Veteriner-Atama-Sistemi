<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Evrak;
use App\Models\EvrakTur;
use App\Models\EvrakDurum;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\NobetHafta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;
use function PHPUnit\Framework\isEmpty;

class VeterinerController extends Controller
{

    public function index()
    {

        $today = date('Y-m-d');

        // NÖBETÇİ VETERİNERLERİ BULMA
        $day_name = date('l'); // İngilizce gün
        $gunler = [
            'Monday' => 'mon',
            'Tuesday' => 'tue',
            'Wednesday' => 'wed',
            'Thursday' => 'thu',
            'Friday' => 'fri',
            'Saturday' => 'sat',
            'Sunday' => 'sun',
        ];
        $today_name =  $gunler[$day_name]; // Türkçeye çevir

        $week = NobetHafta::where('startOfWeek', '<=', $today)
            ->where('endOfWeek', '>=', $today)->first();

        $today_nobetcileri_arr = [];    // veterinerlerin id listesi
        if (isset($week)) {
            $today_nobetciler = [];
            switch ($today_name) {
                case 'mon':
                    $today_nobetciler[] = $week->mon;

                    break;
                case 'tue':
                    $today_nobetciler[] = $week->tue;

                    break;
                case 'wed':
                    $today_nobetciler[] = $week->wed;

                    break;
                case 'thu':
                    $today_nobetciler[] = $week->thu;

                    break;
                case 'fri':
                    $today_nobetciler[] = $week->fri;

                    break;
                case 'sat':
                    $today_nobetciler[] = $week->sat;

                    break;
                case 'sun':
                    $today_nobetciler[] = $week->sun;

                    break;
                default:
                    return redirect()->back()->with('error', 'Hatalı gün sorgusu!');
            }

            foreach ($today_nobetciler[0] as $nobetci) {
                $today_nobetcileri_arr[] = $nobetci['vet_id'];
            }
        }

        // Tüm veterinerler
        $veterinerler = User::role('veteriner')
            ->where("status", 1)
            ->with('evraks.evrak_durumu')
            ->get();


        // izinli veterinerleri bulma
        $izinliler = [];
        foreach ($veterinerler as $vet) {
            $is_izinli = $vet->izins()->wherePivot('startDate','<=',$today)->wherePivot('endDate','>=',$today)->get();
            if(!($is_izinli->isEmpty())){
                $izinliler[] = $vet->id;
            }
        }


        // EVRAK BİTİRME YÜZDESİ HESAPLAMA
        $ortalamalar = [];
        foreach ($veterinerler as $user) {

            if ($user->evraks()->exists()) {
                $onaylanacak = 0;
                $onaylandi = 0;

                foreach ($user->evraks as $evrak) {
                    $durum = $evrak->evrak_durumu->evrak_durum;
                    if (isset($durum)) {
                        if ($durum == "Onaylanacak") {
                            $onaylanacak += 1;
                        } else {
                            $onaylandi += 1;
                        }
                    }
                }
                $yuzde = 0;

                if ($onaylandi != 0) {
                    $yuzde = round($onaylandi / ($onaylanacak + $onaylandi), 2) * 100;
                }

                $ortalamalar[] = $yuzde;
            } else {
                $ortalamalar[] = -1;
            }
        }
        $data['yuzdeler'] = $ortalamalar;

        // VETERİNER BİLGİLERİNİN TEKRAR PAKETLENMESİ
        $veterinerler = collect($veterinerler)->map(function ($vet) use ($today_nobetcileri_arr,$izinliler) {
            return [
                'id' => $vet->id,
                'name' => $vet->name,
                'is_nobetci' => in_array($vet->id, $today_nobetcileri_arr),
                'is_izinli' => in_array($vet->id,$izinliler),
                'created_at' => $vet->created_at,

            ];
        });

        $data['veterinerler'] = $veterinerler;


        return view('admin.veteriners.index', $data);
    }

    public function create()
    {
        return view('admin.veteriners.create');
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

            return redirect()->route('admin.veteriners.index')->with('success', 'Veteriner Başarıyla Ekledi!');
        }

        $vet = new User;
        $vet->name = $request->name;
        $vet->username = $request->username;
        $vet->email = $request->email;
        $vet->password = bcrypt($request->password);
        $vet->phone_number = $request->phone_number;
        $vet->assignRole('veteriner');

        $vet->save();

        return redirect()->route('admin.veteriners.index')->with('success', 'Veteriner Başarıyla Ekledi!');
    }

    public function evraks_list($id)
    {
        $data['veteriner'] = User::with('evraks')->find($id);

        return view('admin.veteriners.veteriner.evraks.index', $data);
    }

    public function edit($id)
    {
        $data['veteriner'] = User::with('evraks')->find($id);
        return view('admin.veteriners.veteriner.edit', $data);
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
            return redirect()->route('admin.veteriners.index')->with('success', 'Veteriner Bilgileri Başarıyla Güncellendi!');
        } else {
            return redirect()->back()->with('error', 'Lütfen Bilgileri Kontrol Ederek Tekrar Deneyiniz!');
        }
    }


    public function evrak_edit($id)
    {

        $data['evrak'] = Evrak::find($id);
        $data['veteriners'] = User::role('veteriner')->get();
        $data['evrak_turs'] = EvrakTur::where('status', true)->get();

        return view('admin.veteriners.veteriner.evraks.edit', $data);
    }

    public function evrak_edited(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'siraNo' => 'required',
            'vgbOnBildirimNo' => 'required',
            'ithalatTür' => 'required',
            'vetSaglikSertifikasiNo' => 'required',
            'vekaletFirmaKisiId' => 'required',
            'urunAdi' => 'required',
            'kategoriId' => 'required',
            'gtipNo' => 'required',
            'urunKG' => 'required',
            'sevkUlke' => 'required',
            'orjinUlke' => 'required',
            'aracPlaka' => 'required',
            'girisGumruk' => 'required',
            'cıkısGumruk' => 'required',
            'veterinerId' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $message = 'Eksik Veri Kaydı! Lütfen Bilgileri Kontrol Edip Tekrar Deneyiniz';
            return redirect()->back()->with('error', $errors);
        }

        $evrak = Evrak::find($request->id);

        $evrak->siraNo = $request->siraNo;
        $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
        $evrak->ithalatTür = $request->ithalatTür;
        $evrak->vetSaglikSertifikasiNo = $request->vetSaglikSertifikasiNo;
        $evrak->vekaletFirmaKisiId = $request->vekaletFirmaKisiId;
        $evrak->urunAdi = $request->urunAdi;
        $evrak->kategoriId = $request->kategoriId;
        $evrak->gtipNo = $request->gtipNo;
        $evrak->urunKG = $request->urunKG;
        $evrak->sevkUlke = $request->sevkUlke;
        $evrak->orjinUlke = $request->orjinUlke;
        $evrak->aracPlaka = $request->aracPlaka;
        $evrak->girisGumruk = $request->girisGumruk;
        $evrak->cıkısGumruk = $request->cıkısGumruk;
        $evrak->tarih = Carbon::now();

        $veteriner = User::find($request->veterinerId);
        $saved = $veteriner->evraks()->save($evrak);

        $isRead = $evrak->evrak_durumu->isRead;
        $evrak->evrak_durumu()->delete();

        $evrak_durum = new EvrakDurum;  // Evrak Durumu güncelleme
        $evrak_durum->isRead = $isRead;
        $evrak_durum->evrak_durum = $request->evrak_durum;
        $evrak->evrak_durumu()->save($evrak_durum);


        if ($saved) {
            return redirect()->route('admin.veteriners.veteriner.evraks', $veteriner->id)->with('success', 'Evrak Başarıyla Düzenlendi.');
        } else {
            return redirect()->back()->with('error', 'Evrak Düzenleme Sırasında Hata Oluştu! Lütfen Bilgilerinizi Kontrol Ediniz.');
        }
    }

    public function evrak_detail($id)
    {
        $data['evrak'] = Evrak::find($id);

        return view('admin.veteriners.veteriner.evraks.detail', $data);
    }



    public function delete($id)
    {

        $veteriner = User::find($id);
        $veteriner->status = 0;
        $veteriner->izins()->detach();


        $veteriner->save();
        return redirect()->route('admin.veteriners.index')->with('success', 'Veteriner Başarıyla Sistemden Çıkarılmıştır!');
    }
}
