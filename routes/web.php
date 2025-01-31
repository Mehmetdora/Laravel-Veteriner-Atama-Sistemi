<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication;


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

});

Route::middleware(['auth', 'role:veteriner'])->group(function () {

    Route::controller(AdminController::class)->group(function(){
        Route::get('/veteriner/dashboard','dashboard')->name('veteriner_dashboard');


    });

});
