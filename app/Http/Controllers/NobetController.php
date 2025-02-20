<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\NobetHafta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NobetController extends Controller
{
    public function index()
    {

        $data['nobetci_haftalari'] = NobetHafta::all();
        $data['vets'] = User::role('veteriner')->where('status', 1)->get();


        return view('admin.nobets.index', $data);
    }


    public function nobet_edited(Request $request)
    {


        try {
            $modifiedWeeks = $request->input('modifiedWeeks');

            if (!$modifiedWeeks || count($modifiedWeeks) == 0) {
                return response()->json(['success' => false, 'message' => 'Değişiklik yapılmadı!: Değişiklik yapmak için mevcut haftadan en fazla 2 hafta sonrası ve 2 hafta öncesi arasında yapılan işlemler kaydedilir.']);
            }

            foreach ($modifiedWeeks as $week) {
                // Varsayılan olarak boş gün dizileri oluştur
                $data = [
                    'weekName'    => $week['weekName'],
                    'startOfWeek' => $week['startOfWeek'],
                    'endOfWeek'   => $week['endOfWeek'],
                    'sun'         => [],
                    'mon'         => [],
                    'tue'         => [],
                    'wed'         => [],
                    'thu'         => [],
                    'fri'         => [],
                    'sat'         => [],
                ];

                // Günlük nöbetçileri uygun günlere ekleyelim
                foreach ($week['events'] as $event) {
                    $eventDate = \Carbon\Carbon::parse($event['date']); // Tarihi Carbon ile parse et
                    $dayOfWeek = strtolower($eventDate->format('D')); // Günün adını al (mon, tue, wed, ...)

                    // Eğer geçerli bir gün ise, ilgili günün listesine ekle
                    if (array_key_exists($dayOfWeek, $data)) {
                        $data[$dayOfWeek][] = [
                            'vet_name' => $event['vet_name'],
                            'date'     => $event['date'],
                        ];
                    }
                }

                // Veriyi kaydet veya güncelle
                $updated_week = NobetHafta::updateOrCreate(
                    ['weekName' => $week['weekName']], // Güncellenecek veya oluşturulacak kayıt kriteri
                    $data
                );
            }

            return response()->json(['success' => true, 'message' => 'Nöbetçi kayıtları başarıyla kaydedildi!']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }
}
