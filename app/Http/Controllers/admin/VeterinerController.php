<?php

namespace App\Http\Controllers\admin;

use App\Models\Urun;
use App\Models\User;
use App\Models\Evrak;
use App\Models\UsksNo;
use App\Models\EvrakTur;
use App\Models\GemiIzni;
use App\Models\EvrakDurum;
use App\Models\NobetHafta;
use App\Models\AracPlakaKg;
use App\Models\EvrakIthalat;
use App\Models\EvrakTransit;

use App\Models\GirisAntrepo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\SaglikSertifika;
use function PHPSTORM_META\map;
use Illuminate\Validation\Rule;
use App\Models\EvrakCanliHayvan;
use App\Models\EvrakAntrepoCikis;
use App\Models\EvrakAntrepoGiris;
use App\Models\EvrakAntrepoVaris;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\EvrakAntrepoVarisDis;

use App\Models\EvrakCanliHayvanGemi;
use Illuminate\Support\Facades\Hash;
use App\Models\EvrakAntrepoSertifika;
use function PHPUnit\Framework\isEmpty;

use Illuminate\Support\Facades\Validator;
use App\Providers\CanliHGemiIzinDuzenleme;
use App\Providers\EvrakVeterineriDegisirseWorkloadGuncelleme;

class VeterinerController extends Controller
{

    protected $gemi_izni_duzenleme;
    protected $evrak_vet_degisirse_worklaods_updater;


    function __construct(CanliHGemiIzinDuzenleme $canliHGemiIzinDuzenleme, EvrakVeterineriDegisirseWorkloadGuncelleme $evrak_veterineri_degisirse_workload_guncelleme)
    {
        $this->evrak_vet_degisirse_worklaods_updater = $evrak_veterineri_degisirse_workload_guncelleme;
        $this->gemi_izni_duzenleme = $canliHGemiIzinDuzenleme;
    }
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
        $evrak_istatistikleri = [];
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

                $evrak_istatistikleri[] = [
                    'toplam' => ($islemde + $onaylandi),
                    'onaylandi' => $onaylandi,
                    'islemde' => $islemde,
                    'yuzde' => $yuzde
                ];
            } else {
                $evrak_istatistikleri[] = [
                    'toplam' => 0,
                    'onaylandi' => 0,
                    'islemde' => 0,
                    'yuzde' => 0
                ];
            }
        }
        $data['evraks_info'] = $evrak_istatistikleri;

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
        $today = Carbon::now();

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


        /*
        Yeni bir veteriner oluşturma sırasında yeni veterinerin workload değerini sıfırdan
        başlatıp tüm evrakların bu veterinere atanamasını önlemek için diğer veterinerlerin
        bu yılki workloadlarının iş yükü ortalamalasrını alarak bu değerden başlatılmalı.
        */

        $all_vets = User::role('veteriner')
            ->where("status", 1)
            ->with('workloads')
            ->get();

        $toplam_workload_degeri = 0;
        $vets_count = count($all_vets);
        foreach ($all_vets as $vet) {
            $workload = $vet->workloads->where('year', $today->year)->first();
            if (!$workload) {
                continue;
            }
            $toplam_workload_degeri += $workload->year_workload;
        }
        $avarage_vets_workload_value = round($toplam_workload_degeri / $vets_count);




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

            // Yeni gelen veterinerin workload değerini ortalama değerden başlatma
            $workload = $user_old->workloads()->create([
                'year' => $today->year,
                'year_workload' => $avarage_vets_workload_value,
                'total_workload' => 0
            ]);

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

        // Yeni gelen veterinerin workload değerini ortalama değerden başlatma
        $workload = $vet->workloads()->create([
            'year' => $today->year,
            'year_workload' => $avarage_vets_workload_value,
            'total_workload' => 0
        ]);

        return redirect()->route('admin.veteriners.index')->with('success', 'Veteriner Başarıyla Ekledi!');
    }

    public function evraks_list($id)
    {
        $data['veteriner'] = User::with([
            'evraks' => function ($query) {
                $query->orderBy('created_at', 'desc');
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
        ], [
            'username.required' => 'Ad-Soyad, alanı eksik!',
            'name.required' => 'Kullanıcı adı, alanı eksik!',
            'email.required' => 'Kullanıcı Email, alanı eksik!',
            'email.email' => 'Lütfen girilen email in doğru formatta olduğunu kontrol ediniz!',
            'phone_number.required' => 'Telefon numarası, alanı eksik!',

        ]);


        // Eğer hata varsa, geriye yönlendir ve tüm hataları göster
        $errors = $validator->errors()->all();
        if (!empty($errors)) {
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


    public function evrak_edit($type, $evrak_id)
    {

        $type = explode("\\", $type);
        $type = end($type);

        if ($type == "EvrakIthalat") {
            $data['evrak'] = EvrakIthalat::with(['urun', 'aracPlakaKgs', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakTransit") {
            $data['evrak'] = EvrakTransit::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoGiris") {
            $data['evrak'] = EvrakAntrepoGiris::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoVaris") {
            $data['evrak'] = EvrakAntrepoVaris::with(['veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoVarisDis") {
            $data['evrak'] = EvrakAntrepoVarisDis::with(['veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
            $antrepo = GirisAntrepo::find($data['evrak']->giris_antrepo_id);
            $data['antrepo_name'] = $antrepo->name;
        } else if ($type == "EvrakAntrepoSertifika") {
            $data['evrak'] = EvrakAntrepoSertifika::with(['urun', 'veteriner.user',  'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakAntrepoCikis") {
            $evrak = EvrakAntrepoCikis::with(['urun', 'veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
            $data['evrak'] = $evrak;
            $data['usks'] = UsksNo::find($evrak->usks_id);
        } else if ($type == "EvrakCanliHayvan") {
            $data['evrak'] = EvrakCanliHayvan::with(['urun', 'veteriner.user', 'evrak_durumu', 'saglikSertifikalari'])
                ->find($evrak_id);
        } else if ($type == "EvrakCanliHayvanGemi") {
            $evrak = EvrakCanliHayvanGemi::with(['veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
            $data['evrak'] = $evrak;
            $data['start_date'] = Carbon::parse($evrak->start_date)->format('m/d/Y');
        }


        $data['evrak_type'] = $type;
        $data['veteriners'] = User::role('veteriner')->where('status', 1)->get();
        $data['uruns'] = Urun::all();
        $data['giris_antrepos'] = GirisAntrepo::actives();

        return view('admin.veteriners.veteriner.evraks.edit', $data);
    }

    public function evrak_edited(Request $request)
    {
        $errors = [];

        // Validation
        if ($request->type == "EvrakIthalat") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'ss_no' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'arac_plaka_kg' => 'required',
                'girisGumruk' => 'required',
                'is_numuneli' => 'required',
            ], [
                'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                'ss_no.required' => 'Sağlık Sertifikası, alanı eksik!',
                'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                'arac_plaka_kg.required' => 'Araç Plakası ve Yük Miktarı(KG), alanı eksik!',
                'girisGumruk.required' => 'Giriş Gümrüğü, alanı eksik!',
                'is_numuneli.required' => 'Numuneli/Numunesiz, alanı eksik!',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == 'EvrakTransit') {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'ss_no' => 'required',
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
            ], [
                'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                'ss_no.required' => 'Sağlık Sertifikası, alanı eksik!',
                'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                'aracPlaka.required' => 'Araç Plakası veya Konteyner No, alanı eksik!',
                'girisGumruk.required' => 'Giriş Gümrüğü, alanı eksik!',
                'cikisGumruk.required' => 'Çıkış Gümrüğü, alanı eksik!',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoGiris") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'ss_no' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'aracPlaka' => 'required',
                'girisGumruk' => 'required',
                'varis_antrepo_id' => 'required',
            ], [
                'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                'ss_no.required' => 'Sağlık Sertifikası, alanı eksik!',
                'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                'aracPlaka.required' => 'Araç Plakası veya Konteyner No, alanı eksik!',
                'girisGumruk.required' => 'Giriş Gümrüğü, alanı eksik!',
                'varis_antrepo_id.required' => 'Varış Antrepo, alanı eksik!',
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
                'urunlerinBulunduguAntrepo' => 'required',
            ], [
                'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                'oncekiVGBOnBildirimNo.required' => 'Önceki VGB Numarası, alanı eksik!',
                'vetSaglikSertifikasiNo.required' => 'Sağlık Sertifikası, alanı eksik!',
                'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                'urunlerinBulunduguAntrepo.required' => 'Giriş Antrepo, alanı eksik!',

            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoSertifika") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vetSaglikSertifikasiNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'orjinUlke' => 'required',
                'aracPlaka' => 'required',
                'cikis_antrepo' => 'required',
            ], [
                'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                'vetSaglikSertifikasiNo.required' => 'Sağlık Sertifikası, alanı eksik!',
                'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                'aracPlaka.required' => 'Araç Plakası veya Konteyner No, alanı eksik!',
                'cikis_antrepo.required' => 'Çıkış Antreposu, alanı eksik!',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoCikis") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'usks_no' => 'required',
                'usks_miktar' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'aracPlaka' => 'required',
                'cikisGumruk' => 'required',
            ], [
                'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                'usks_no.required' => 'USKS Numarası, alanı eksik!',
                'usks_miktar.required' => 'USKS Miktarı, alanı eksik!',
                'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                'aracPlaka.required' => 'Araç Plakası veya Konteyner No, alanı eksik!',
                'cikisGumruk.required' => 'Çıkış Gümrüğü, alanı eksik!',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakCanliHayvan") {
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'vgbOnBildirimNo' => 'required',
                'vetSaglikSertifikasiNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'urun_kategori_id' => 'required',
                'gtipNo' => 'required',
                'hayvanSayisi' => 'required',
                'sevkUlke' => 'required',
                'orjinUlke' => 'required',
                'girisGumruk' => 'required',
                'cikisGumruk' => 'required',
            ], [
                'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                'vgbOnBildirimNo.required' => 'VGB Ön Bildirim Numarası, alanı eksik!',
                'vetSaglikSertifikasiNo.required' => 'Sağlık Sertifikası, alanı eksik!',
                'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                'urun_kategori_id.required' => 'Ürünün Kategorisi, alanı eksik!',
                'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                'hayvanSayisi.required' => 'Başvuru Yapılan Hayvan Sayısı(Baş Sayısı), alanı eksik!',
                'sevkUlke.required' => 'Sevk Eden Ülke, alanı eksik!',
                'orjinUlke.required' => 'Orjin Ülke, alanı eksik!',
                'girisGumruk.required' => 'Giriş Gümrüğü, alanı eksik!',
                'cikisGumruk.required' => 'Çıkış Gümrüğü, alanı eksik!',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakCanliHayvanGemi") {
            $validator = Validator::make($request->all(), [
                'hayvan_sayisi' => 'required',
                'veteriner_id' => 'required',
                'start_date' => 'required',
                'day_count' => 'required',
            ], [
                'hayvan_sayisi' => 'Hayvan Sayısı, alanı eksik!',
                'veteriner_id' => 'Veteriner Hekim, alanı eksik!',
                'start_date' => 'Başlangıç Tarihi, alanı eksik!',
                'day_count' => 'Kaç Günlük, alanı eksik!',
            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        } elseif ($request->type == "EvrakAntrepoVarisDis") {  //Evrak Antrepo Varış(DIŞ)
            $validator = Validator::make($request->all(), [
                'siraNo' => 'required',
                'oncekiVGBOnBildirimNo' => 'required',
                'vetSaglikSertifikasiNo' => 'required',
                'vekaletFirmaKisiAdi' => 'required',
                'urunAdi' => 'required',
                'gtipNo' => 'required',
                'urunKG' => 'required',
                'urunlerinBulunduguAntrepo' => 'required',
            ], [
                'siraNo.required' => 'Evrak Kayıt No, alanı eksik!',
                'oncekiVGBOnBildirimNo.required' => 'Önceki VGB Numarası, alanı eksik!',
                'vetSaglikSertifikasiNo.required' => 'Sağlık Sertifikası, alanı eksik!',
                'vekaletFirmaKisiAdi.required' => 'Vekalet Sahibi Firma / Kişi İsmi, alanı eksik!',
                'urunAdi.required' => 'Ürünün Adı, alanı eksik!',
                'gtipNo.required' => 'G.T.İ.P. No İlk 4 Rakamı, alanı eksik!',
                'urunKG.required' => 'Ürünün Kg Cinsinden Net Miktarı, alanı eksik!',
                'urunlerinBulunduguAntrepo.required' => 'Giriş Antrepo, alanı eksik!',

            ]);
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
            }
        }

        // Eğer hata varsa, geriye yönlendir ve tüm hataları göster
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->with('error', $errors);
        }

        dd($request->all());


        DB::beginTransaction();

        try {

            if ($request->type == "EvrakIthalat") {


                $evrak = EvrakIthalat::find($request->input('id'));
                $old_is_numuneli = $evrak->is_numuneli;

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = (int)str_replace('.', '', $request->urunKG);
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->is_numuneli = $request->is_numuneli;
                if ($request->is_numuneli) {
                    $evrak->difficulty_coefficient = 40;
                }
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);


                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;

                // numunesizden -> numuneliye
                if ($request->is_numuneli != $old_is_numuneli && $request->is_numuneli == true) {  // Evrak türü değişmiş ise

                    // Veteriner değişmişse worklaod güncelleme
                    if ($user_evrak->user_id != (int)$request->veterinerId) {
                        $this->evrak_vet_degisirse_worklaods_updater
                            ->veterinerlerin_worklaods_guncelleme(
                                $user_evrak->user_id,
                                (int)$request->veterinerId,
                                'ithalat',
                                'numuneli_ithalat'
                            );
                    } else {  // veteriner değişmemişse
                        $this->evrak_vet_degisirse_worklaods_updater
                            ->veterinerlerin_worklaods_guncelleme(
                                $user_evrak->user_id,
                                $user_evrak->user_id,
                                'ithalat',
                                'numuneli_ithalat'
                            );
                    }

                    // numuneliden -> numunesize
                } elseif ($request->is_numuneli != $old_is_numuneli && $request->is_numuneli == false) {
                    // Veteriner değişmişse worklaod güncelleme
                    if ($user_evrak->user_id != (int)$request->veterinerId) {
                        $this->evrak_vet_degisirse_worklaods_updater
                            ->veterinerlerin_worklaods_guncelleme(
                                $user_evrak->user_id,
                                (int)$request->veterinerId,
                                'numuneli_ithalat',
                                'ithalat'
                            );
                    } else {  // veteriner değişmemişse
                        $this->evrak_vet_degisirse_worklaods_updater
                            ->veterinerlerin_worklaods_guncelleme(
                                $user_evrak->user_id,
                                $user_evrak->user_id,
                                'numuneli_ithalat',
                                'ithalat'
                            );
                    }

                    // sadece veteriner değişmişse
                } else {
                    $evrak_type = $evrak->is_numuneli ? 'numuneli_ithalat' : 'ithalat';
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            $evrak_type,
                            $evrak_type
                        );
                }
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

                // Gelen plaka ID'lerini al
                $yeni_plakalar = [];
                $plakalar = json_decode($request->arac_plaka_kg) ?? [];
                $plaka_ids = [];
                foreach ($plakalar as $plaka) {
                    if (!isset($plaka->id) || $plaka->id == -1) {
                        $yeni_plakalar[] = $plaka;
                    } else {
                        $plaka_ids[] = $plaka->id;
                    }
                }

                // Silinmesi gerekenleri silme
                $evrak->aracPlakaKgs()
                    ->whereNotIn('arac_plaka_kgs.id', $plaka_ids)
                    ->delete();

                foreach ($yeni_plakalar as $plaka) {
                    AracPlakaKg::create([
                        'arac_plaka' => $plaka->plaka,
                        'miktar' => $plaka->miktar,
                        'evrak_ithalat_id' => $evrak->id
                    ]);
                }

                // SErtifika update
                $sertifika = $evrak->saglikSertifikalari()->first();
                $sertifika->ssn = $request->ss_no;
                $sertifika->toplam_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->kalan_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->save();
            } elseif ($request->type == "EvrakTransit") {
                $evrak = EvrakTransit::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = (int)str_replace('.', '', $request->urunKG);
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
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'transit',
                            'transit'
                        );
                }
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

                // SErtifika update
                $sertifika = $evrak->saglikSertifikalari()->first();
                $sertifika->ssn = $request->ss_no;
                $sertifika->toplam_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->kalan_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->save();
            } elseif ($request->type == "EvrakAntrepoGiris") {
                $evrak = EvrakAntrepoGiris::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = (int)str_replace('.', '', $request->urunKG);
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->giris_antrepo_id = $request->varis_antrepo_id;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);


                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'antrepo_giris',
                            'antrepo_giris'
                        );
                }
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

                // SErtifika update
                $sertifika = $evrak->saglikSertifikalari()->first();
                $sertifika->ssn = $request->ss_no;
                $sertifika->toplam_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->kalan_miktar = (int)str_replace('.', '', $request->urunKG);
                $sertifika->save();
            } elseif ($request->type == "EvrakAntrepoVaris") {

                $evrak = EvrakAntrepoVaris::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->oncekiVGBOnBildirimNo = $request->oncekiVGBOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->urunlerinBulunduguAntrepo = $request->urunlerinBulunduguAntrepo;
                $evrak->save();

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'antrepo_varis',
                            'antrepo_varis'
                        );
                }
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
                // Sağlık sertifikalarını silmeden önce hangilerinin silinip hangilerinin kalacağına karar verme

                // Gelen sağlık sertifikalarının ID'lerini al
                $yeni_sertifikalar = [];
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo) ?? [];
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {
                    if (!isset($sertifika->id) || $sertifika->id == -1) {
                        $yeni_sertifikalar[] = $sertifika;
                    } else {
                        $sertifika_ids[] = $sertifika->id;
                    }
                }

                // Silinmesi gerekenleri silme
                $evrak->saglikSertifikalari()
                    ->whereNotIn('saglik_sertifikas.id', $sertifika_ids)
                    ->delete();

                foreach ($yeni_sertifikalar as $sertifika) {

                    $evrak->saglikSertifikalari()->create([
                        'ssn' => $sertifika->ssn,
                        'toplam_miktar' => $sertifika->miktar,
                        'kalan_miktar' => $sertifika->miktar,
                    ]);
                }
            } elseif ($request->type == "EvrakAntrepoVarisDis") {  //Evrak Antrepo Varış Dış

                $evrak = EvrakAntrepoVarisDis::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->oncekiVGBOnBildirimNo = $request->oncekiVGBOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;

                // yeni bir antrepo girilmiş ise bunu db ekle
                $gelen_antrepo = GirisAntrepo::where('name', $request->urunlerinBulunduguAntrepo)->first();
                if (!$gelen_antrepo) {    // DB de yoksa ekle
                    $gelen_antrepo = new GirisAntrepo;
                    $gelen_antrepo->name = $request->urunlerinBulunduguAntrepo;
                    $gelen_antrepo->save();
                }
                $evrak->giris_antrepo_id = $gelen_antrepo->id;

                $evrak->save();

                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'antrepo_varis_dis',
                            'antrepo_varis_dis'
                        );
                }
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
                // Sağlık sertifikalarını silmeden önce hangilerinin silinip hangilerinin kalacağına karar verme

                // Gelen sağlık sertifikalarının ID'lerini al
                $yeni_sertifikalar = [];
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo) ?? [];
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {
                    if (!isset($sertifika->id) || $sertifika->id == -1) {
                        $yeni_sertifikalar[] = $sertifika;
                    } else {
                        $sertifika_ids[] = $sertifika->id;
                    }
                }

                // Silinmesi gerekenleri silme
                $evrak->saglikSertifikalari()
                    ->whereNotIn('saglik_sertifikas.id', $sertifika_ids)
                    ->delete();

                foreach ($yeni_sertifikalar as $sertifika) {

                    $evrak->saglikSertifikalari()->create([
                        'ssn' => $sertifika->ssn,
                        'toplam_miktar' => $sertifika->miktar,
                        'kalan_miktar' => $sertifika->miktar,
                    ]);
                }
            } elseif ($request->type == "EvrakAntrepoSertifika") {


                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo) ?? [];

                $evrak = EvrakAntrepoSertifika::find($request->input('id'));
                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = $request->urunKG;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;


                // yeni bir antrepo girilmiş ise bunu db ekle
                $gelen_antrepo = GirisAntrepo::where('name', $request->cikis_antrepo)->first();
                if (!$gelen_antrepo) {    // DB de yoksa ekle
                    $gelen_antrepo = new GirisAntrepo;
                    $gelen_antrepo->name = $request->cikis_antrepo;
                    $gelen_antrepo->save();
                }
                $evrak->cikisAntrepo = $gelen_antrepo->name;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }
                $evrak->urun()->sync([$urun->id]);

                $usks = $evrak->usks;
                if (!$usks) {
                    throw new \Exception('Evrakla ilişkili USKS verisine erişilemedi, Lütfen tekrar deneyiniz!');
                }
                $usks->miktar = $request->urunKG;
                $usks->save();


                //Sağlık sertifikalarını kaydetme
                // Sağlık sertifikalarını silmeden önce hangilerinin silinip hangilerinin kalacağına karar verme
                // Gelen sağlık sertifikalarının ID'lerini al
                $yeni_sertifikalar = [];
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo) ?? [];
                if (count($sertifikalar) == 0) {
                    throw new \Exception('Lütfen evrağa en az 1 adet sağlık sertifikası giriniz!');
                }

                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {
                    if (!isset($sertifika->id) || $sertifika->id == -1) {   // yeni gelen sertifika
                        $yeni_sertifikalar[] = $sertifika;
                    } else {        // Zaten kayıtlı sertifikalar
                        $sertifika_ids[] = $sertifika->id;
                    }
                }


                $silinen_sertifikalar = $evrak->saglikSertifikalari()
                    ->whereNotIn('saglik_sertifikas.id', $sertifika_ids)->get();


                // amaç silinen bir sertifikanın miktarını geri antrpeo girişteki sertifikanın miktarına eklemek
                // silinen sertifikalarda ana sertifikaya geri iade işlemi yapılacak
                foreach ($silinen_sertifikalar as $sertifika) {
                    $antp_giris_sertifika = SaglikSertifika::whereHas('evraks_giris', function ($query) {})
                        ->where('ssn', $sertifika->ssn)
                        ->with('evraks_giris')
                        ->first();
                    if ($antp_giris_sertifika->evraks_giris) {    // girilen sertifikaların ssn numaraları ile ana sertifikalar bulunuyor
                        $antp_giris_sertifika->kalan_miktar += $sertifika->toplam_miktar;
                        $antp_giris_sertifika->save();
                    }
                }


                // Sertifikanın miktarını tekrar güncelledikten sonra silinecekler silinebilir
                $evrak->saglikSertifikalari()
                    ->whereNotIn('saglik_sertifikas.id', $sertifika_ids)
                    ->delete();




                // yeni eklenen sertifikalarda ana sertifikadan düşülecek
                // değişiklik yapılmayan sertifikalarda yine bir değişiklik yapılmayacak
                // En son yeni gelen evraklar ekleniyor
                foreach ($yeni_sertifikalar as $sertifika) {

                    $evrak->saglikSertifikalari()->create([
                        'ssn' => $sertifika->ssn,
                        'toplam_miktar' => $sertifika->miktar,
                        'kalan_miktar' => $sertifika->miktar,
                    ]);


                    $giris_sertifikası = SaglikSertifika::whereHas('evraks_giris', function ($query) {})
                        ->where('ssn', $sertifika->ssn)
                        ->with('evraks_giris')
                        ->first();
                    if ($giris_sertifikası) {
                        $giris_sertifikası->kalan_miktar -= $sertifika->miktar;
                        $giris_sertifikası->save();
                    }
                }


                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'antrepo_sertifika',
                            'antrepo_sertifika'
                        );
                    $user_evrak->user_id = (int)$request->veterinerId;
                }
                $user_evrak->evrak()->associate($evrak);

                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydedilemedi!");
                }

                // Evrak durumunu kaydetme
                $evrak_durum = $evrak->evrak_durumu;
                $evrak_durum->evrak_durum = $request->evrak_durum;
                $evrak->evrak_durumu()->save($evrak_durum);
            } elseif ($request->type == "EvrakAntrepoCikis") {
                $evrak = EvrakAntrepoCikis::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->urunKG = (int)str_replace('.', '', $request->urunKG);
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->aracPlaka = $request->aracPlaka;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();


                // Veterineri usks numarası üzerinden antrepo sertifika bulunarak bu evrağı alan veterinere atanacak
                $usks = UsksNo::find($evrak->usks_id);
                $usks->usks_no = $request->usks_no;
                $usks->miktar = (int)str_replace('.', '', $request->usks_miktar);
                $usks->save();

                // Ürün modelini bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }
                $evrak->urun()->sync([$urun->id]);

                // Atanacak olan veteriner gelen veteriner hangisi ise ona atanır
                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'antrepo_cikis',
                            'antrepo_cikis'
                        );
                }
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
            } elseif ($request->type == "EvrakCanliHayvan") {

                $evrak = EvrakCanliHayvan::find($request->input('id'));

                $evrak->evrakKayitNo = $request->siraNo;
                $evrak->vgbOnBildirimNo = $request->vgbOnBildirimNo;
                $evrak->vekaletFirmaKisiAdi = $request->vekaletFirmaKisiAdi;
                $evrak->urunAdi = $request->urunAdi;
                $evrak->gtipNo = $request->gtipNo;
                $evrak->hayvanSayisi = $request->hayvanSayisi;
                $evrak->sevkUlke = $request->sevkUlke;
                $evrak->orjinUlke = $request->orjinUlke;
                $evrak->girisGumruk = $request->girisGumruk;
                $evrak->cikisGumruk = $request->cikisGumruk;
                $evrak->save();

                // İlişkili modelleri bağlama
                $urun = Urun::find($request->urun_kategori_id);
                if (!$urun) {
                    throw new \Exception("Gerekli ilişkili veriler bulunamadı!");
                }

                $evrak->urun()->sync([$urun->id]);



                $user_evrak = $evrak->veteriner;
                // Veteriner değişmişse worklaod güncelleme
                if ($user_evrak->user_id != (int)$request->veterinerId) {
                    $this->evrak_vet_degisirse_worklaods_updater
                        ->veterinerlerin_worklaods_guncelleme(
                            $user_evrak->user_id,
                            (int)$request->veterinerId,
                            'canli_hayvan',
                            'canli_hayvan'
                        );
                }
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
                // Sağlık sertifikalarını silmeden önce hangilerinin silinip hangilerinin kalacağına karar verme

                // Gelen sağlık sertifikalarının ID'lerini al
                $yeni_sertifikalar = [];
                $sertifikalar = json_decode($request->vetSaglikSertifikasiNo) ?? [];
                $sertifika_ids = [];
                foreach ($sertifikalar as $sertifika) {
                    if (!isset($sertifika->id) || $sertifika->id == -1) {
                        $yeni_sertifikalar[] = $sertifika;
                    } else {
                        $sertifika_ids[] = $sertifika->id;
                    }
                }

                // Silinmesi gerekenleri silme
                $evrak->saglikSertifikalari()
                    ->whereNotIn('saglik_sertifikas.id', $sertifika_ids)
                    ->delete();

                foreach ($yeni_sertifikalar as $sertifika) {

                    $evrak->saglikSertifikalari()->create([
                        'ssn' => $sertifika->ssn,
                        'miktar' => $sertifika->miktar,
                    ]);
                }
            } elseif ($request->type == "EvrakCanliHayvanGemi") {

                $old_start_date = null;
                $old_vet_id = null;
                $old_hayvan_s = null;


                $evrak = EvrakCanliHayvanGemi::find($request->id);
                $old_start_date = $evrak->start_date;
                $old_vet_id = $evrak->veteriner->user->id;
                $old_hayvan_s = (int)str_replace('.', '', $request->hayvan_sayisi);

                $evrak->hayvan_sayisi = (int)str_replace('.', '', $request->hayvan_sayisi);
                $evrak->start_date = Carbon::createFromFormat('m/d/Y', $request->start_date);
                $evrak->day_count = (int)$request->day_count;
                $evrak->save();



                // Veteriner ile evrak kaydetme
                $user_evrak = $evrak->veteriner;
                $user_evrak->user_id = $request->veterinerId;
                $user_evrak->evrak()->associate($evrak);
                $saved = $user_evrak->save();
                if (!$saved) {
                    throw new \Exception("Evrak kaydı sırasında beklenmedik bir hata oluştu, Lütfen bilgilerinizi kontrol edip tekrar deneyiniz!");
                }

                // Evrak durumu değişmeyecek , veterinerler işi bitirince bitmiş olacak


                // Gemi izni düzenleme
                $gemi_izin = GemiIzni::where('veteriner_id', $old_vet_id)
                    ->where('start_date', $old_start_date)->first();

                $this->gemi_izni_duzenleme->canli_h_gemi_izin_düzenle(
                    $gemi_izin,
                    $request->veterinerId,
                    Carbon::createFromFormat('m/d/Y', $request->start_date),
                    (int)$request->day_count
                );


                // Veterinerin worklaod güncelleme
                if ($request->veterinerId != $old_vet_id) {  // veteriner değişmişse
                    // eski veterinerin workload ını azalt, yenisini arttır.

                    $old_veteriner = User::find($old_vet_id);
                    $old_workload = $old_veteriner->veterinerinBuYilkiWorkloadi();
                    if ($old_hayvan_s < 15000) {
                        $old_workload->year_workload -= 150;
                        $old_workload->total_workload -= 150;
                        if ($old_workload->temp_workload != 0) {
                            $old_workload->temp_workload -= 150;
                        }
                    } elseif ($old_hayvan_s > 15000) {
                        $old_workload->year_workload -= 300;
                        $old_workload->total_workload -= 300;
                        if ($old_workload->temp_workload != 0) {
                            $old_workload->temp_workload -= 300;
                        }
                    }
                    $old_workload->save();


                    $veteriner = User::find($request->veterinerId);
                    $workload = $veteriner->veterinerinBuYilkiWorkloadi();
                    if ($request->hayvan_sayisi > 0 && $request->hayvan_sayisi <= 15000) {
                        $workload->year_workload += 150;
                        $workload->total_workload += 150;
                        if ($workload->temp_workload != 0) {
                            $workload->temp_workload += 150;
                        }
                    } elseif ($request->hayvan_sayisi > 15000) {
                        $workload->year_workload += 300;
                        $workload->total_workload += 300;
                        if ($workload->temp_workload != 0) {
                            $workload->temp_workload += 300;
                        }
                    }
                    $workload->save();
                }
            }

            DB::commit();

            return redirect()->route('admin.veteriners.veteriner.evraks', (int)$request->veterinerId)->with('success', "Evrak başarıyla düzenlendi.");
        } catch (\Exception $e) {

            DB::rollBack();     // veritabanını eski haline getirme - hata olmsı durumunda
            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
    }

    public function evrak_detail($type, $evrak_id)
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
        } else if ($type == "EvrakAntrepoVarisDis") {
            $data['evrak'] = EvrakAntrepoVarisDis::with(['veteriner.user', 'evrak_durumu'])
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
        } else if ($type == "EvrakCanliHayvanGemi") {
            $data['evrak'] = EvrakCanliHayvanGemi::with(['veteriner.user', 'evrak_durumu'])
                ->find($evrak_id);
        }

        $data['type'] = $type;

        return view('admin.veteriners.veteriner.evraks.detail', $data);
    }



    public function delete($id)
    {

        $veteriner = User::find($id);
        $veteriner->status = 0;
        $veteriner->izins()->detach();
        $veteriner->nobets()->delete();
        $veteriner->workloads()->delete();
        $veteriner->gemi_izins()->delete();


        $veteriner->save();
        return redirect()->route('admin.veteriners.index')->with('success', 'Veteriner Başarıyla Sistemden Çıkarılmıştır!');
    }
}
