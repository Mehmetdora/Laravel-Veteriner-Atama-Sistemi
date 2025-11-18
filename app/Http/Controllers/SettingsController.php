<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Spatie\Backup\BackupFacade as Backup;

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


    public function manuel_backup()
    {
        try {
            // Backup öncesi log

            Log::info('Manuel yedekleme başlatılıyor...');
            Log::info('PHP sürümü: ' . PHP_VERSION);
            Log::info('İşletim sistemi: ' . PHP_OS);

            // Backup dizinini kontrol et
            $backupPath = storage_path('app/private/Laravel');
            Log::info('Backup path: ' . $backupPath);
            Log::info('Dizin var mı: ' . (is_dir($backupPath) ? 'Evet' : 'Hayır'));
            Log::info('Dizin yazılabilir mi: ' . (is_writable($backupPath) ? 'Evet' : 'Hayır'));

            // Artisan komutunu çalıştır
            $exitCode = Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true,
            ]);

            $output = Artisan::output();
            Log::info('Backup command output: ' . $output);
            Log::info('Backup command exit code: ' . $exitCode);

            if ($exitCode !== 0) {
                return redirect()->back()->with('error', 'Yedekleme sırasında komut hatası oluştu. Detay: ' . $output . ' - 001');
            }

            // Oluşturulan dosyayı bul
            $backupPath = storage_path('app/private/Laravel');

            // Dizin var mı kontrol et
            if (!is_dir($backupPath)) {
                Log::error('Backup dizini bulunamadı: ' . $backupPath);
                return redirect()->back()->with('error', 'Yedekleme sırasından yedekleme dosyalarının bulunacağı dizin bulunamadı. - 002');
            }

            $files = scandir($backupPath, SCANDIR_SORT_DESCENDING);

            $latestBackup = null;
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && str_ends_with($file, '.zip')) {
                    $latestBackup = $file;
                    break;
                }
            }

            if ($latestBackup) {
                Log::info('Yedekleme başarıyla oluşturuldu: ' . $latestBackup);
                return redirect()->back()->with('success', 'Yedekleme başarıyla oluşturuldu: ' . $latestBackup);
            } else {
                Log::warning('Yedekleme komutu çalıştı ama zip dosyası bulunamadı.');
                return redirect()->back()->with('error', 'Yedekleme dosyası oluşturulamadı. Lütfen log dosyalarını kontrol edin. - 003');
            }
        } catch (\Exception $e) {
            Log::error('manuel_backup exception: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', "Hata: " . $e->getMessage() . " - 004");
        }
    }


    public function restore(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'backup_file' => 'required|file|max:51200', // max 50 MB
        ], [
            'backup_file.required' => 'Lütfen bir dosya seçiniz!',
        ]);

        // Uzantıyı manuel kontrol et
        if ($request->hasFile('backup_file')) {
            $extension = $request->file('backup_file')->getClientOriginalExtension();
            if (strtolower($extension) !== 'sql') {
                return redirect()->back()->with('error', 'Lütfen .sql uzantılı bir dosya seçiniz!');
            }
        }


        // Eğer hata varsa, geriye yönlendir ve tüm hataları göster
        $errors = $validator->errors()->all();
        if (!empty($errors)) {
            return redirect()->back()->with('error', $errors);
        }


        $file = $request->file('backup_file');
        $path = $file->getRealPath();

        // SQL içeriğini oku
        $sql = file_get_contents($path);

        // PHP limitlerini artırmak (gerekirse)
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        try {
            DB::unprepared($sql);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Veritabanı geri yükleme başarısız: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Veritabanı başarıyla geri yüklendi!');
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
