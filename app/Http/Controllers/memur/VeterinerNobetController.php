<?php

namespace App\Http\Controllers\memur;

use App\Models\User;
use App\Models\Nobet;
use App\Models\NobetList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VeterinerNobetController extends Controller
{
    public function index()
    {
        $data['vets'] = User::role('veteriner')->where('status', 1)->with(['nobets'])->get();
        return view('memur.nobets.index', $data);
    }


    public function nobet_created(Request $request)
    {
        try {
            $vet = User::find($request->input('vet_id'));
            $vet->nobets()->create([
                'date' => $request->input('date')
            ]);

            return response()->json(['success' => true, 'message' => 'Nöbetçi başarıyla kaydedildi!']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }

    public function nobet_edited(Request $request)
    {
        try {
            $vet = User::find($request->input('vet_id'));
            $nobet = $vet->nobets()->where('date', $request->input('old_date'))->first();
            $nobet->date = $request->input('new_date');
            $nobet->save();

            return response()->json(['success' => true, 'message' => 'Nöbetçi başarıyla yeniden düzenlendi!']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }

    public function nobet_deleted(Request $request)
    {
        try {
            $vet = User::find($request->input('vet_id'));
            $nobet = $vet->nobets()->where('date', $request->input('date'))->first();
            $nobet->delete();

            return response()->json(['success' => true, 'message' => 'Nöbetçi başarıyla silindi!']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }


    public function create_multiple()
    {


        $plan = NobetList::latest('id')->first();
        $list = $plan?->list ?? [];
        $day_1 = array_values($list)[0];

        //dd($list);

        $vets = User::role('veteriner')->where('status', 1)->get();

        $data['vets'] = $vets;
        $data['list'] = $list;
        $data['day_1'] = $day_1;


        return view("memur.nobets.create", $data);
    }

    public function created_multiple(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'day_11' => 'required|array|min:1',
            'day_12' => 'required|array|min:1',
            'day_13' => 'required|array|min:1',
            'day_14' => 'required|array|min:1',
            'day_15' => 'required|array|min:1',
            'day_16' => 'required|array|min:1',
            'day_17' => 'required|array|min:1',
            'day_21' => 'required|array|min:1',
            'day_31' => 'required|array|min:1',
            'day_41' => 'required|array|min:1',
            'day_22' => 'required|array|min:1',
            'day_32' => 'required|array|min:1',
            'day_42' => 'required|array|min:1',
            'day_23' => 'required|array|min:1',
            'day_33' => 'required|array|min:1',
            'day_43' => 'required|array|min:1',
            'day_24' => 'required|array|min:1',
            'day_34' => 'required|array|min:1',
            'day_44' => 'required|array|min:1',
            'day_25' => 'required|array|min:1',
            'day_35' => 'required|array|min:1',
            'day_45' => 'required|array|min:1',
            'day_26' => 'required|array|min:1',
            'day_36' => 'required|array|min:1',
            'day_46' => 'required|array|min:1',
            'day_27' => 'required|array|min:1',
            'day_37' => 'required|array|min:1',
            'day_47' => 'required|array|min:1',
        ], [
            'day_11.required' => 'Hafta 1, gün 1 boş bırakılamaz!',
            'day_21.required' => 'Hafta 2, gün 1 boş bırakılamaz!',
            'day_31.required' => 'Hafta 3, gün 1 boş bırakılamaz!',
            'day_41.required' => 'Hafta 4, gün 1 boş bırakılamaz!',
            'day_12.required' => 'Hafta 1, gün 2 boş bırakılamaz!',
            'day_22.required' => 'Hafta 2, gün 2 boş bırakılamaz!',
            'day_32.required' => 'Hafta 3, gün 2 boş bırakılamaz!',
            'day_42.required' => 'Hafta 4, gün 2 boş bırakılamaz!',
            'day_13.required' => 'Hafta 1, gün 3 boş bırakılamaz!',
            'day_23.required' => 'Hafta 2, gün 3 boş bırakılamaz!',
            'day_33.required' => 'Hafta 3, gün 3 boş bırakılamaz!',
            'day_43.required' => 'Hafta 4, gün 3 boş bırakılamaz!',
            'day_14.required' => 'Hafta 1, gün 4 boş bırakılamaz!',
            'day_24.required' => 'Hafta 2, gün 4 boş bırakılamaz!',
            'day_34.required' => 'Hafta 3, gün 4 boş bırakılamaz!',
            'day_44.required' => 'Hafta 4, gün 4 boş bırakılamaz!',
            'day_15.required' => 'Hafta 1, gün 5 boş bırakılamaz!',
            'day_25.required' => 'Hafta 2, gün 5 boş bırakılamaz!',
            'day_35.required' => 'Hafta 3, gün 5 boş bırakılamaz!',
            'day_45.required' => 'Hafta 4, gün 5 boş bırakılamaz!',
            'day_16.required' => 'Hafta 1, gün 6 boş bırakılamaz!',
            'day_26.required' => 'Hafta 2, gün 6 boş bırakılamaz!',
            'day_36.required' => 'Hafta 3, gün 6 boş bırakılamaz!',
            'day_46.required' => 'Hafta 4, gün 6 boş bırakılamaz!',
            'day_17.required' => 'Hafta 1, gün 7 boş bırakılamaz!',
            'day_27.required' => 'Hafta 2, gün 7 boş bırakılamaz!',
            'day_37.required' => 'Hafta 3, gün 7 boş bırakılamaz!',
            'day_47.required' => 'Hafta 4, gün 7 boş bırakılamaz!',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->all());
        }



        $yarin = now()->setTimezone('Europe/Istanbul')->addDay(); // tam saat, yarın

        $dayMap = [
            'day_11' => 0,
            'day_12' => 1,
            'day_13' => 2,
            'day_14' => 3,
            'day_15' => 4,
            'day_16' => 5,
            'day_17' => 6,

            'day_21' => 7,
            'day_22' => 8,
            'day_23' => 9,
            'day_24' => 10,
            'day_25' => 11,
            'day_26' => 12,
            'day_27' => 13,

            'day_31' => 14,
            'day_32' => 15,
            'day_33' => 16,
            'day_34' => 17,
            'day_35' => 18,
            'day_36' => 19,
            'day_37' => 20,

            'day_41' => 21,
            'day_42' => 22,
            'day_43' => 23,
            'day_44' => 24,
            'day_45' => 25,
            'day_46' => 26,
            'day_47' => 27,
        ];


        $list = [];

        foreach ($dayMap as $key => $offset) {
            $date = $yarin->copy()->addDays($offset)->toDateString(); // "Y-m-d"
            $vetIds = (array) $request->input($key, []);

            // İstersen temizle: string -> int, tekrarları sil
            $vetIds = array_values(array_unique(array_map('intval', $vetIds)));

            $list[$date] = $vetIds;
        }


        // tek kayıt olduğu için id si 1 olan var mı bak yoksa oluştur,varsa güncelle
        NobetList::updateOrCreate(
            ['id' => 1], // tek kayıt mantığı
            [
                'start_date' => $yarin->toDateString(),
                'end_date'   => $yarin->copy()->addDays(27)->toDateString(),
                'list'       => $list,
            ]
        );

        return redirect()->back()->with('success', 'Nöbet listesi başarıyla oluşturuldu-düzenlendi, kullanılabilir.');
    }

    public function apply_multiple()
    {

        $list_obj = NobetList::latest('id')->firstOrFail();

        // cast varsa direkt array gelir, yoksa decode et:
        $list = is_array($list_obj->list) ? $list_obj->list : json_decode($list_obj->list, true);

        foreach ($list as $date => $vetIds) {
            foreach ($vetIds as $vetId) {

                Nobet::firstOrCreate([
                    'user_id' => (int) $vetId,
                    'date'    => $date, // "Y-m-d"
                ]);
            }
        }


        return redirect()->route('memur.nobet.veteriner.index')->with('success', "Geçerli nöbet listesi yarından itibaren 28 günlük olarak başarıyla takvime işlendi.");
    }
}
