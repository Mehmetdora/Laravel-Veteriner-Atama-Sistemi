<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\EvrakController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\memur\MemurController;
use App\Http\Controllers\admin\EvrakTurController;
use App\Http\Controllers\admin\izin\MemurIzinController;
use App\Http\Controllers\admin\izin\VeterinerIzinController;
use App\Http\Controllers\admin\MemurController as AdminMemurController;
use App\Http\Controllers\admin\nobet\MemurNobetController;
use App\Http\Controllers\admin\nobet\VeterinerNobetController;
use App\Http\Controllers\admin\UrunController;
use App\Http\Controllers\veteriner\VeterinerController;
use App\Http\Controllers\admin\VeterinerController as VeterinerEController;


    // HER MİGRATE:FRESH YAPINCA SEEDER ile  BERABER ÇALIŞTIR


Route::controller(Authentication::class)->group(function(){

    Route::get('/','login')->name('login');
    Route::post('/girildi','logined')->name('logined');
    Route::get('/çıkış','logout')->name('logout');

});




Route::middleware(['auth', 'role:admin'])->group(function () {

    // Admin Genel ve Profil İşlemleri
    Route::controller(AdminController::class)->group(function(){
        Route::get('/admin/anasayfa','dashboard')->name('admin_dashboard');

        Route::get('/admin/profil','profile')->name('admin_profile');

        Route::get('/admin/profil/düzenle','edit')->name('admin_edit');
        Route::post('/admin/profil/düzenlendi','edited')->name('admin_edited');
    });

    // Evrak İşlemleri
    Route::controller(EvrakController::class)->group(function(){
        Route::get('/admin/evrak/liste','index')->name('admin.evrak.index');
        Route::get('/admin/evrak/detay/{type}/{id}','detail')->name('admin.evrak.detail');

        Route::get('/admin/evrak/düzenle/{type}/{id}','edit')->name('admin.evrak.edit');
        Route::post('/admin/evrak/düzenlendi','edited')->name('admin.evrak.edited');

        Route::get('/admin/evrak/ekle','create')->name('admin.evrak.create');
        Route::post('/admin/evrak/eklendi','created')->name('admin.evrak.created');
    });

    // Evrak Türleri İşlemleri
    Route::controller(EvrakTurController::class)->group(function(){
        Route::get('/admin/evrak-tur/liste','index')->name('admin.evrak_tur.index');
        Route::get('/admin/evrak-tur/sil/{id}','delete')->name('admin.evrak_tur.delete');

        Route::get('/admin/evrak-tur/düzenle/{id}','edit')->name('admin.evrak_tur.edit');
        Route::post('/admin/evrak-tur/düzenlendi','edited')->name('admin.evrak_tur.edited');

        Route::get('/admin/evrak-tur/ekle','create')->name('admin.evrak_tur.create');
        Route::post('/admin/evrak-tur/eklendi','created')->name('admin.evrak_tur.created');

    });

    // Ürün İşlemleri
    Route::controller(UrunController::class)->group(function(){
        Route::get('/admin/ürün/liste','index')->name('admin.uruns.index');
        Route::get('/admin/ürün/sil/{id}','delete')->name('admin.uruns.delete');

        Route::get('/admin/ürün/düzenle/{id}','edit')->name('admin.uruns.edit');
        Route::post('/admin/ürün/düzenlendi','edited')->name('admin.uruns.edited');

        Route::get('/admin/ürün/ekle','create')->name('admin.uruns.create');
        Route::post('/admin/ürün/eklendi','created')->name('admin.uruns.created');

    });

    // Admin Veteriner işlemleri
    Route::controller(VeterinerEController::class)->group(function(){
        Route::get('/admin/veterinerler/liste','index')->name('admin.veteriners.index');

        Route::get('/admin/veterinerler/ekle','create')->name('admin.veteriners.create');
        Route::post('/admin/veterinerler/eklendi','created')->name('admin.veteriners.created');


        Route::get('/admin/veterinerler/veteriner/evraklar{id}','evraks_list')->name('admin.veteriners.veteriner.evraks');
        Route::get('/admin/veterinerler/veteriner/evrak/{id}/düzenle','evrak_edit')->name('admin.veteriners.veteriner.evrak.edit');
        Route::get('/admin/veterinerler/veteriner/evrak/{id}/detay','evrak_detail')->name('admin.veteriners.veteriner.evrak.detail');
        Route::post('/admin/veterinerler/veteriner/evrak/düzenlendi','evrak_edited')->name('admin.veteriners.veteriner.evrak.edited');

        Route::get('/admin/veterinerler/veteriner/düzenle/{id}','edit')->name('admin.veteriners.veteriner.edit');
        Route::post('/admin/veterinerler/veteriner/düzenlendi','edited')->name('admin.veteriners.veteriner.edited');

        Route::get('/admin/veterinerler/veteriner/sil/{id}','delete')->name('admin.veteriners.veteriner.delete');
    });

    // Admin Memur işlemleri
    Route::controller(AdminMemurController::class)->group(function(){
        Route::get('/admin/memurlar/liste','index')->name('admin.memurs.index');

        Route::get('/admin/memurlar/ekle','create')->name('admin.memurs.create');
        Route::post('/admin/memurlar/eklendi','created')->name('admin.memurs.created');

        Route::get('/admin/memurlar/memur/düzenle/{id}','edit')->name('admin.memurs.memur.edit');
        Route::post('/admin/memurlar/memur/düzenlendi','edited')->name('admin.memurs.memur.edited');

        Route::get('/admin/memurlar/memur/sil/{id}','delete')->name('admin.memurs.memur.delete');
    });


    // Nöbet işlemleri
    Route::controller(VeterinerNobetController::class)->group(function(){
        Route::get('/admin/veteriner/nöbet/liste','index')->name('admin.nobet.veteriner.index');
        Route::post('/admin/veteriner/nöbet/eklendi','nobet_created')->name('admin.nobet.veteriner.created');
        Route::post('/admin/veteriner/nöbet/düzenlendi','nobet_edited')->name('admin.nobet.veteriner.edited');
        Route::post('/admin/veteriner/nöbet/silindi','nobet_deleted')->name('admin.nobet.veteriner.deleted');
    });



    // İzin işlemleri
    Route::controller(VeterinerIzinController::class)->group(function(){
        Route::get('/admin/veteriner/izin/liste','index')->name('admin.izin.veteriner.index');
        Route::get('/admin/veteriner/izin/ekle','create')->name('admin.izin.veteriner.create');
        Route::post('/admin/veteriner/izin/eklendi','created')->name('admin.izin.veteriner.created');
        Route::post('/admin/veteriner/izin/silindi','delete')->name('admin.izin.veteriner.delete');

    });
    Route::controller(MemurIzinController::class)->group(function(){
        Route::get('/admin/memur/izin/liste','index')->name('admin.izin.memur.index');
        Route::get('/admin/memur/izin/ekle','create')->name('admin.izin.memur.create');
        Route::post('/admin/memur/izin/eklendi','created')->name('admin.izin.memur.created');
        Route::post('/admin/memur/izin/silindi','delete')->name('admin.izin.memur.delete');

    });

});






Route::middleware(['auth', 'role:veteriner'])->group(function () {

    // Veteriner Actions controller
    Route::controller(VeterinerController::class)->group(function(){
        Route::get('/veteriner/anasayfa','dashboard')->name('veteriner_dashboard');

        Route::get('/veteriner/profil','profile_index')->name('veteriner.profile.index');

        Route::get('/veteriner/profil/düzenle','profile_edit')->name('veteriner.profile.edit');
        Route::post('/veteriner/profil/düzenlendi','profile_edited')->name('veteriner.profile.edited');

        Route::get('/veteriner/evraklar/liste','evraks_index')->name('veteriner.evraks.index');
        Route::get('/veteriner/evraklar/{id}/detay','evrak_index')->name('veteriner.evraks.evrak.index');

        Route::get('veteriner/nöbetler/liste','nobets_index')->name('veteriner.nobet.index');
        Route::get('veteriner/izinler/liste','izins_index')->name('veteriner.izin.index');

    });

});





Route::middleware(['auth', 'role:memur'])->group(function () {

    Route::controller(MemurController::class)->group(function(){
        Route::get('/memur/anasayfa','dashboard')->name('memur_dashboard');

        Route::get('/memur/profil','profile_index')->name('memur.profile.index');

        Route::get('/memur/profil/düzenle','profile_edit')->name('memur.profile.edit');
        Route::post('/memur/profil/düzenlendi','profile_edited')->name('memur.profile.edited');

        Route::get('memur/izinler/liste','izins_index')->name('memur.izin.index');

    });

});
