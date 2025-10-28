<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;
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


    public function manuel_backup()
    {
        try {
            $timestamp = now()->format('Y-m-d-H-i-s');
            $sqlFileName = "Manuel-Backup-{$timestamp}.sql";
            $zipFileName = "Manuel-Backup-{$timestamp}.zip";

            $dir = storage_path('app/private/Laravel');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $sqlFilePath = $dir . DIRECTORY_SEPARATOR . $sqlFileName;
            $zipFilePath = $dir . DIRECTORY_SEPARATOR . $zipFileName;
            $errorPath = $dir . DIRECTORY_SEPARATOR . 'error.log';

            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', 3306);
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD', '');

            // mysqldump binary yolu (sisteminizdeki doğru yolu kullanın)
            $mysqldump = '/opt/homebrew/bin/mysqldump';

            // Güvenlik: argümanları escape et
            $mysqldumpEscaped = escapeshellcmd($mysqldump);
            $userArg = '--user=' . escapeshellarg($dbUser);
            $hostArg = '--host=' . escapeshellarg($dbHost);
            $portArg = '--port=' . escapeshellarg($dbPort);
            $protocolArg = '--protocol=TCP';
            $dbArg = escapeshellarg($dbName);
            $outFile = escapeshellarg($sqlFilePath);
            $errFile = escapeshellarg($errorPath);

            if (empty($dbPass)) {
                $command = "{$mysqldumpEscaped} {$protocolArg} {$userArg} {$hostArg} {$portArg} {$dbArg} > {$outFile} 2> {$errFile}";
            } else {
                // MYSQL_PWD kullanımı: parola shell içinde güvenli biçimde geçirilir
                $mysqlPwd = 'MYSQL_PWD=' . escapeshellarg($dbPass);
                $command = "{$mysqlPwd} {$mysqldumpEscaped} {$protocolArg} {$userArg} {$hostArg} {$portArg} {$dbArg} > {$outFile} 2> {$errFile}";
            }

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorLog = file_exists($errorPath) ? file_get_contents($errorPath) : 'Hata logu bulunamadı';
                Log::error('Backup error: ' . $errorLog);
                // Hata dosyasını temizle (isteğe bağlı)
                if (file_exists($errorPath)) {
                    @unlink($errorPath);
                }
                return redirect()->back()->with('error', 'Yedekleme hatası (kod: ' . $returnVar . '): ' . Str::limit($errorLog, 1000));
            }

            // Hata dosyasını temizle
            if (file_exists($errorPath)) {
                @unlink($errorPath);
            }

            // ZipArchive ile .sql dosyasını zip'e koy
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                // Eğer zip açılamadıysa sql dosyasını silmeyin, hata döndürün
                Log::error("Zip açma/oluşturma hatası: {$zipFilePath}");
                return redirect()->back()->with('error', 'Zip oluşturulamadı.');
            }

            // Zip içine sql dosyasını ekle. İkinci parametre zip içindeki dosya adı olur.
            $added = $zip->addFile($sqlFilePath, $sqlFileName);
            $zip->close();

            if ($added === false) {
                Log::error('SQL dosyası zip içine eklenemedi: ' . $sqlFilePath);
                return redirect()->back()->with('error', 'SQL dosyası zip içine eklenemedi.');
            }

            // Başarılıysa orijinal .sql dosyasını kaldır (isteğe bağlı)
            if (file_exists($sqlFilePath)) {
                @unlink($sqlFilePath);
            }

            Log::info('Yedekleme (zip) oluşturuldu: ' . $zipFilePath);

            return redirect()->back()->with('success', 'Yedekleme başarıyla oluşturuldu: ' . $zipFileName);
        } catch (\Exception $e) {
            Log::error('manuel_backup exception: ' . $e->getMessage());
            return redirect()->back()->with('error', "Hata: " . $e->getMessage());
        }
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
