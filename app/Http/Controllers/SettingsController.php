<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;

use Illuminate\Validation\Rule;
use function Laravel\Prompts\text;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{


    private function formatBytes($bytes, $decimals = 2)
    {
        $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        if ($bytes == 0) return '0 B';
        $factor = floor(log($bytes, 1024));
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $sizes[$factor];
    }


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



        $path = storage_path('app/private/Laravel');
        $files = scandir($path);

        $zipFiles = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || !str_ends_with($file, '.zip')) continue;

            $fullPath = $path . '/' . $file;
            $sizeInBytes = filesize($fullPath);

            $zipFiles[] = [
                'name' => $file,
                'size' => $this->formatBytes($sizeInBytes),
            ];
        }

        $data['zipFiles'] = $zipFiles;


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




    public function download($file)
    {


        $path = storage_path('app/private/Laravel/' . $file);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Dosya bulunamadı.');
        }

        return response()->download($path);
    }
}
