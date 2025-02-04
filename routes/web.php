<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\EvrakController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\VeterinerController;

    // HER MİGRATE:FRESH YAPINCA SEEDER ile  BERABER ÇALIŞTIR


Route::controller(Authentication::class)->group(function(){
    Route::get('/','login')->name('login');
    Route::post('/logined','logined')->name('logined');

    Route::get('/logout','logout')->name('logout');
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::controller(AdminController::class)->group(function(){
        Route::get('/admin/dashboard','dashboard')->name('admin_dashboard');



    });

    Route::controller(EvrakController::class)->group(function(){
        Route::get('/admin/evrak/liste','index')->name('admin.evrak.index');
        Route::get('/admin/evrak/detay/{id}','detail')->name('admin.evrak.detail');

        Route::get('/admin/evrak/düzenle/{id}','edit')->name('admin.evrak.edit');
        Route::post('/admin/evrak/düzenlendi','edited')->name('admin.evrak.edited');

        Route::get('/admin/evrak/ekle','create')->name('admin.evrak.create');
        Route::post('/admin/evrak/eklendi','created')->name('admin.evrak.created');
    });

    Route::controller(VeterinerController::class)->group(function(){
        Route::get('/admin/veteriner/liste','index')->name('admin.veteriner.index');
        
    });

});

Route::middleware(['auth', 'role:veteriner'])->group(function () {

    Route::controller(AdminController::class)->group(function(){
        Route::get('/veteriner/dashboard','dashboard')->name('veteriner_dashboard');


    });

});
