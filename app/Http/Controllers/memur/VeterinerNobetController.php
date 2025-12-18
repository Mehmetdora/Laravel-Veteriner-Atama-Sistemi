<?php

namespace App\Http\Controllers\memur;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
