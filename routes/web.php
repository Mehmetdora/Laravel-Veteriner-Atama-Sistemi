<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\EvrakController;
use App\Http\Controllers\VeterinerController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\EvrakTurController;
use App\Http\Controllers\admin\VeterinerController as VeterinerEController;
use App\Http\Controllers\memur\MemurController;

    // HER MİGRATE:FRESH YAPINCA SEEDER ile  BERABER ÇALIŞTIR


Route::controller(Authentication::class)->group(function(){
    Route::get('/giris','login')->name('login');
    Route::post('/giriss','logined')->name('logined');

    Route::get('/logout','logout')->name('logout');
});




Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::controller(AdminController::class)->group(function(){
        Route::get('/admin/anasayfa','dashboard')->name('admin_dashboard');
    });




    Route::controller(EvrakController::class)->group(function(){
        Route::get('/admin/evrak/liste','index')->name('admin.evrak.index');
        Route::get('/admin/evrak/detay/{id}','detail')->name('admin.evrak.detail');

        Route::get('/admin/evrak/düzenle/{id}','edit')->name('admin.evrak.edit');
        Route::post('/admin/evrak/düzenlendi','edited')->name('admin.evrak.edited');

        Route::get('/admin/evrak/ekle','create')->name('admin.evrak.create');
        Route::post('/admin/evrak/eklendi','created')->name('admin.evrak.created');
    });

    Route::controller(EvrakTurController::class)->group(function(){
        Route::get('/admin/evrak-tur/liste','index')->name('admin.evrak_tur.index');
        Route::get('/admin/evrak-tur/sil/{id}','delete')->name('admin.evrak_tur.delete');

        Route::get('/admin/evrak-tur/düzenle/{id}','edit')->name('admin.evrak_tur.edit');
        Route::post('/admin/evrak-tur/düzenlendi','edited')->name('admin.evrak_tur.edited');

        Route::get('/admin/evrak-tur/ekle','create')->name('admin.evrak_tur.create');
        Route::post('/admin/evrak-tur/eklendi','created')->name('admin.evrak_tur.created');

    });



    // Admin Veteriner işlemleri controller
    Route::controller(VeterinerEController::class)->group(function(){
        Route::get('/admin/veteriner/liste','index')->name('admin.veteriner.index');

        Route::get('/admin/veteriner/ekle','create')->name('admin.veteriner.create');
        Route::post('/admin/veteriner/eklendi','created')->name('admin.veteriner.created');
    });

});




Route::middleware(['auth', 'role:veteriner'])->group(function () {

    // Veteriner Actions controller
    Route::controller(VeterinerController::class)->group(function(){
        Route::get('/veteriner/anasayfa','dashboard')->name('veteriner_dashboard');
    });

});





Route::middleware(['auth', 'role:memur'])->group(function () {

    Route::controller(MemurController::class)->group(function(){
        Route::get('/memur/anasayfa','dashboard')->name('memur_dashboard');
    });

});
