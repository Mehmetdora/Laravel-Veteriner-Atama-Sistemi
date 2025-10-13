<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use App\Http\Middleware\RunScheduledTasks;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )->withSchedule(function (Schedule $schedule) {


        // oluşturulan schedule'lar burada kullanılacak. (mesela $schedule->command() ile)


        $frequency = 'weekly'; // default haftalık

        // proje ilk defa başlatılırken tablolar olmayacağı için bunu kontrol et, yoksa default değeri kullan
        if (Schema::hasTable('settings')) {
            $frequency = DB::table('settings')
                ->where('key', 'backup_frequency')
                ->value('value') ?? 'weekly';
        }

        $command = $schedule->command('backup:run --only-db')
            ->withoutOverlapping()
            ->timezone('Europe/Istanbul');


        // Dinamik olarak sıklığı ayarla
        switch ($frequency) {
            case 'daily':
                $command->dailyAt('12:00');
                break;
            case 'weekly':
                $command->weekly(); // Haftalık
                break;
            case 'monthly':
                $command->monthly(); // Aylık
                break;
            case 'hourly':
                $command->hourly(); // Saatlik
                break;
            default:
                $command->weekly(); // Tanımsızsa yine haftalık
                break;
        }
    })
    ->withMiddleware(function (Middleware $middleware) {


        // Her istek geldiğinde bu middleware de çalışacak
        $middleware->web(append: [
            RunScheduledTasks::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
