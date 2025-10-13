<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;

use Illuminate\Validation\Rule;
use function Laravel\Prompts\text;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {


        $yedekeleme = SystemSetting::where('key', 'backup_frequency')->first();
        $text = '';


        if ($yedekeleme->value == 'daily') {
            $text = 'Günlük';
        } elseif ($yedekeleme->value == 'weekly') {
            $text = 'Haftada Bir';
        } elseif ($yedekeleme->value == 'monthly') {
            $text = 'Ayda Bir';
        } elseif ($yedekeleme->value == 'hourly') {
            $text = 'Saatlik';
        } else {
            $text = 'Tanımlanmamış';
        }


        $data['backup_description'] = $text;

        return view('admin.system_settings.index', $data);
    }
    public function edit()
    {
        $yedekeleme = SystemSetting::where('key', 'backup_frequency')->first();
        $text = '';


        if ($yedekeleme->value == 'daily') {
            $text = 'Günlük';
        } elseif ($yedekeleme->value == 'weekly') {
            $text = 'Haftada Bir';
        } elseif ($yedekeleme->value == 'monthly') {
            $text = 'Ayda Bir';
        } elseif ($yedekeleme->value == 'hourly') {
            $text = 'Saatlik';
        } else {
            $text = 'Tanımlanmamış';
        }


        $data['backup_description'] = $text;
        $data['setting'] = $yedekeleme;


        return view('admin.system_settings.edit', $data);
    }
    public function edited(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'backup_frequency' => 'required',
        ], [
            'backup_frequency.required' => 'Yedekleme döngüsünü seçiniz!',
        ]);


        // Eğer hata varsa, geriye yönlendir ve tüm hataları göster
        $errors = $validator->errors()->all();
        if (!empty($errors)) {
            return redirect()->back()->with('error', $errors);
        }


        $setting = SystemSetting::where('key', 'backup_frequency')->first();
        $setting->value = $request->backup_frequency;
        $setting->save();


        return redirect()->route('admin.system_settings.index')->with('success', 'Sistem ayarları başarıyla düzenlendi!');
    }
}
