<?php

namespace App\Http\Controllers\admin;

use App\Models\Urun;
use App\Models\User;
use App\Models\Evrak;
use App\Models\EvrakTur;
use App\Models\EvrakDurum;
use App\Models\NobetHafta;
use App\Models\EvrakIthalat;
use App\Models\EvrakTransit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\SaglikSertifika;

use function PHPSTORM_META\map;
use Illuminate\Validation\Rule;
use App\Models\EvrakAntrepoCikis;
use App\Models\EvrakAntrepoGiris;
use App\Models\EvrakAntrepoVaris;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\EvrakAntrepoSertifika;
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
            ->with(['evraks.evrak.evrak_durumu', 'nobets', 'izins'])
            ->get();
        // Her user ın UserEvrak kayıtlarını her kaydın evrak kaydı ile ve her evrak
        //kaydını da evrak_durum kaydı ile birlikte getirme sorgusu


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

                foreach ($user->evraks as $kayit) {
                    $durum = $kayit->evrak->evrak_durumu->evrak_durum;
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
        $data['veteriner'] = User::with([
                'evraks' => function($query){
                    $query->orderBy('created_at','desc');
                },
                'evraks.evrak.evrak_durumu'
            ])->find($id);

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


    public function evrak_edit($type,$evrak_id)
    {

        $type = explode("\\", $type);
        $type = end($type);

        if ($type == "EvrakIthalat") {
            $data['evrak'] = EvrakIthalat::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakTransit") {
            $data['evrak'] = EvrakTransit::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoGiris") {
            $data['evrak'] = EvrakAntrepoGiris::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoVaris") {
            $data['evrak'] = EvrakAntrepoVaris::with([ 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoSertifika") {
            $data['evrak'] = EvrakAntrepoSertifika::with(['urun', 'veteriner.user',  'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoCikis") {
            $data['evrak'] = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        }


        $data['evrak_type'] = $type;
        $data['veteriners'] = User::role('veteriner')->where('status', 1)->get();
        $data['uruns'] = Urun::all();

        return view('admin.veteriners.veteriner.evraks.edit', $data);
    }

    public function evrak_edited(Request $request)
    {
        $errors = [];

        // Validation
        if ($request->type == "EvrakIthalat" || $request->type == "EvrakTransit") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
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
                'cikisGumruk' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoGiris") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
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
                'varisAntreposu' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoVaris") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'oncekiVGBOnBildirimNo' => 'required',
                'vetSaglikSertifikasiNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'girisAntreposu' => 'required',
                'varisAntreposu' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoSertifika") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'USKSSertifikaReferansNo' => 'required',
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
                'cikisGumruk' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoCikis") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
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
                'cikisGumruk' => 'required',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        }

        // Eğer hata varsa, geriye yönlendir ve tüm hataları göster
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->with('error', $errors);
        }


        try {

            if ($request->type == "EvrakIthalat") {

                $evrak = EvrakIthalat::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);



                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                //Sağlık sertifikalarını kaydetme
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo);
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {

                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $sertifika->ssn;
                    $saglik_sertfika->miktar = $sertifika->miktar;
                    $saglik_sertfika->save();
                    $sertifika_ids[] = $saglik_sertfika->id;
                }
                $evrak->saglikSertifikalari()->sync($sertifika_ids);
            } elseif ($request->type == "EvrakTransit") {
                $evrak = EvrakTransit::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);


                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                //Sağlık sertifikalarını kaydetme
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo);
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {

                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $sertifika->ssn;
                    $saglik_sertfika->miktar = $sertifika->miktar;
                    $saglik_sertfika->save();
                    $sertifika_ids[] = $saglik_sertfika->id;
                }
                $evrak->saglikSertifikalari()->sync($sertifika_ids);
            } elseif ($request->type == "EvrakAntrepoGiris") {
                $evrak = EvrakAntrepoGiris::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->varisAntreposu = $request->varisAntreposu;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);


                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                //Sağlık sertifikalarını kaydetme
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo);
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {

                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $sertifika->ssn;
                    $saglik_sertfika->miktar = $sertifika->miktar;
                    $saglik_sertfika->save();
                    $sertifika_ids[] = $saglik_sertfika->id;
                }
                $evrak->saglikSertifikalari()->sync($sertifika_ids);
            } elseif ($request->type == "EvrakAntrepoVaris") {
                $evrak = EvrakAntrepoVaris::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->oncekiVGBOnBildirimNo = $request->oncekiVGBOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->girisAntreposu = $request->girisAntreposu;
                $evrak->varisAntreposu = $request->varisAntreposu;
                $evrak->save();

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                //Sağlık sertifikalarını kaydetme
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo);
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {

                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $sertifika->ssn;
                    $saglik_sertfika->miktar = $sertifika->miktar;
                    $saglik_sertfika->save();
                    $sertifika_ids[] = $saglik_sertfika->id;
                }
                $evrak->saglikSertifikalari()->sync($sertifika_ids);
            } elseif ($request->type == "EvrakAntrepoSertifika") {
                $evrak = EvrakAntrepoSertifika::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->USKSSertifikaReferansNo = $request->USKSSertifikaReferansNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);


                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                //Sağlık sertifikalarını kaydetme
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo);
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {

                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $sertifika->ssn;
                    $saglik_sertfika->miktar = $sertifika->miktar;
                    $saglik_sertfika->save();
                    $sertifika_ids[] = $saglik_sertfika->id;
                }
                $evrak->saglikSertifikalari()->sync($sertifika_ids);
            } elseif ($request->type == "EvrakAntrepoCikis") {
                $evrak = EvrakAntrepoCikis::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);


                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                $user_evrak->user_id = (int)$request->veterinerId;
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);

                //Sağlık sertifikalarını kaydetme
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo);
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {

                    $saglik_sertfika = new SaglikSertifika;
                    $saglik_sertfika->ssn = $sertifika->ssn;
                    $saglik_sertfika->miktar = $sertifika->miktar;
                    $saglik_sertfika->save();
                    $sertifika_ids[] = $saglik_sertfika->id;
                }
                $evrak->saglikSertifikalari()->sync($sertifika_ids);

            }

            return redirect()->route('admin.veteriners.veteriner.evraks',$request->veterinerId)->with('success', "Evrak başarıyla düzenlendi.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
    }

    public function evrak_detail($type,$evrak_id)
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
            $data['evrak'] = EvrakAntrepoVaris::with([ 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoSertifika") {
            $data['evrak'] = EvrakAntrepoSertifika::with(['urun', 'veteriner.user',  'evrak_durumu'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoCikis") {
            $data['evrak'] = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        }

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
