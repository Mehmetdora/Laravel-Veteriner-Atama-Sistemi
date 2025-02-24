<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\EvrakController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\memur\MemurController;
use App\Http\Controllers\admin\EvrakTurController;
use App\Http\Controllers\veteriner\VeterinerController;
use App\Http\Controllers\admin\VeterinerController as VeterinerEController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\NobetController;

    // HER MİGRATE:FRESH YAPINCA SEEDER ile  BERABER ÇALIŞTIR


Route::controller(Authentication::class)->group(function(){

    Route::get('/','login')->name('login');
    Route::post('/giriss','logined')->name('logined');
    Route::get('/logout','logout')->name('logout');

});




Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::controller(AdminController::class)->group(function(){
        Route::get('/admin/anasayfa','dashboard')->name('admin_dashboard');

        Route::get('/admin/profil','profile')->name('admin_profile');

        Route::get('/admin/profil/düzenle','edit')->name('admin_edit');
        Route::post('/admin/profil/düzenlendi','edited')->name('admin_edited');
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


    // Nöbet işlemleri
    Route::controller(NobetController::class)->group(function(){

        Route::get('/admin/nöbet/liste','index')->name('admin.nobets.index');

        Route::post('/admin/nöbet/eklendi','nobet_created')->name('admin.nobet.created');
        Route::post('/admin/nöbet/düzenlendi','nobet_edited')->name('admin.nobet.edited');
        Route::post('/admin/nöbet/silindi','nobet_deleted')->name('admin.nobet.deleted');
    });

    // İzin işlemleri
    Route::controller(IzinController::class)->group(function(){

        Route::get('/admin/izin/liste','index')->name('admin.izin.index');

        Route::get('/admin/izin/ekle','create')->name('admin.izin.create');
        Route::post('/admin/izin/eklendi','created')->name('admin.izin.created');

        Route::post('/admin/izin/silindi','delete')->name('admin.izin.delete');

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

    });

});





Route::middleware(['auth', 'role:memur'])->group(function () {

    Route::controller(MemurController::class)->group(function(){
        Route::get('/memur/anasayfa','dashboard')->name('memur_dashboard');
    });

});
