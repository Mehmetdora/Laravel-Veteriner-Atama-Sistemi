<?php

namespace App\Http\Controllers\admin;

use App\Models\Urun;
use App\Models\User;
use App\Models\Evrak;
use App\Models\EvrakTur;
use App\Models\EvrakDurum;
use App\Models\NobetHafta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use function PHPSTORM_META\map;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Validator;

class VeterinerController extends Controller
{

    public function index()
    {
        date_default_timezone_set('Europe/Istanbul');
        $today = date('Y-m-d');
        $real_time = date('Y-m-d H:i:s');

        // Tüm veterinerler ilişkileri ile
        $veterinerler = User::role('veteriner')
            ->where("status", 1)
            ->with(['evraks.evrak_durumu', 'nobets', 'izins'])
            ->get();


        // izinli veterinerleri ve nobetçi veterinerleri bulma
        $izinliler = [];
        $nobetliler = [];
        foreach ($veterinerler as $vet) {
            $is_izinli = $vet->izins()->wherePivot('startDate', '<=', $real_time)->wherePivot('endDate', '>=', $real_time)->get();
            if (!($is_izinli->isEmpty())) {
                $izinliler[] = $vet->id;
            }

            $is_nobetci = $vet->nobets()->where('date', $today)->exists();
            if ($is_nobetci) {
                $nobetliler[] = $vet->id;
            }
        }


        // EVRAK BİTİRME YÜZDESİ HESAPLAMA
        $ortalamalar = [];
        foreach ($veterinerler as $user) {

            if ($user->evraks()->exists()) {
                $islemde = 0;
                $onaylandi = 0;

                foreach ($user->evraks as $evrak) {
                    $durum = $evrak->evrak_durumu->evrak_durum;
                    if (isset($durum)) {
                        if ($durum == "İşlemde") {
                            $islemde += 1;
                        } else {
                            $onaylandi += 1;
                        }
                    }
                }
                $yuzde = 0;

                if ($onaylandi != 0) {
                    $yuzde = round($onaylandi / ($islemde + $onaylandi), 2) * 100;
                }

                $ortalamalar[] = $yuzde;
            } else {
                $ortalamalar[] = -1;
            }
        }
        $data['yuzdeler'] = $ortalamalar;

        // VETERİNER BİLGİLERİNİN TEKRAR PAKETLENMESİ
        $veterinerler = collect($veterinerler)->map(function ($vet) use ($nobetliler, $izinliler) {
            return [
                'id' => $vet->id,
                'name' => $vet->name,
                'is_nobetci' => in_array($vet->id, $nobetliler),
                'is_izinli' => in_array($vet->id, $izinliler),
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
        $data['veteriners'] = User::role('veteriner')->where('status', 1)->get();
        $data['uruns'] = Urun::all();
        $data['evrak_turs'] = EvrakTur::where('status', true)->get();
        $data['evrak'] = Evrak::with(['urun', 'evrak_tur', 'veteriner'])->find($id);

        return view('admin.veteriners.veteriner.evraks.edit', $data);
    }

    public function evrak_edited(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'siraNo' => 'required',
            'vgbOnBildirimNo' => 'required',
            'evrak_tur_id' => 'required',
            'vetSaglikSertifikasiNo' => 'required',
            'vekaletFirmaKisiAdi' => 'required',
            'urunAdi' => 'required',
            'urun_kategori_id' => 'required',
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
        $evrak->vetSaglikSertifikasiNo = $request->vetSaglikSertifikasiNo;
        $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
        $evrak->urunAdi = $request->urunAdi;
        $evrak->gtipNo = $request->gtipNo;
        $evrak->urunKG = $request->urunKG;
        $evrak->sevkUlke = $request->sevkUlke;
        $evrak->orjinUlke = $request->orjinUlke;
        $evrak->aracPlaka = $request->aracPlaka;
        $evrak->girisGumruk = $request->girisGumruk;
        $evrak->cıkısGumruk = $request->cıkısGumruk;
        $evrak->tarih = Carbon::now();


        $urun = Urun::find($request->urun_kategori_id);
        $evrak->urun()->associate($urun);

        $evrak_tur = EvrakTur::find($request->evrak_tur_id);
        $evrak->evrak_tur()->associate($evrak_tur);

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
        $veteriner->nobets()->delete();


        $veteriner->save();
        return redirect()->route('admin.veteriners.index')->with('success', 'Veteriner Başarıyla Sistemden Çıkarılmıştır!');
    }
}
