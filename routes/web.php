<?php



use App\Http\Controllers\admin\AntrepoStokTakipController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\EvrakController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AntrepoController;
use App\Http\Controllers\memur\MemurController;
use App\Http\Controllers\admin\EvrakTurController;
use App\Http\Controllers\admin\izin\MemurIzinController;
use App\Http\Controllers\admin\izin\VeterinerIzinController;
use App\Http\Controllers\admin\MemurController as AdminMemurController;
use App\Http\Controllers\admin\nobet\MemurNobetController;
use App\Http\Controllers\admin\nobet\VeterinerNobetController;
use App\Http\Controllers\admin\StokTakipController;
use App\Http\Controllers\admin\UrunController;
use App\Http\Controllers\veteriner\VeterinerController;
use App\Http\Controllers\admin\VeterinerController as VeterinerEController;
use App\Http\Controllers\memur\EvrakController as MemurEvrakController;
use App\Http\Controllers\memur\StokTakipController as MemurStokTakipController;

// HER MİGRATE:FRESH YAPINCA SEEDER ile  BERABER ÇALIŞTIR


Route::controller(Authentication::class)->group(function () {

    Route::get('/', 'login')->name('login');
    Route::post('/girildi', 'logined')->name('logined');
    Route::get('/çıkış', 'logout')->name('logout');
});




Route::middleware(['auth', 'role:admin'])->group(function () {

    // Admin Genel ve Profil İşlemleri
    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/anasayfa', 'dashboard')->name('admin_dashboard');

        Route::get('/admin/profil', 'profile')->name('admin_profile');

        Route::get('/admin/profil/düzenle', 'edit')->name('admin_edit');
        Route::post('/admin/profil/düzenlendi', 'edited')->name('admin_edited');
    });

    // Evrak İşlemleri
    Route::controller(EvrakController::class)->group(function () {
        Route::get('/admin/evrak/liste', 'index')->name('admin.evrak.index');
        Route::get('/admin/evrak/detay/{type}/{id}', 'detail')->name('admin.evrak.detail');

        Route::get('/admin/evrak/düzenle/{type}/{id}', 'edit')->name('admin.evrak.edit');
        Route::post('/admin/evrak/düzenlendi', 'edited')->name('admin.evrak.edited');

        Route::get('/admin/evrak/ekle', 'create')->name('admin.evrak.create');
        Route::post('/admin/evrak/eklendi', 'created')->name('admin.evrak.created');

        Route::get('/admin/evrak/sil/{type}/{id}', 'delete')->name('admin.evrak.delete');

        Route::post("/admin/evrak/antrepo-sertifika", 'get_evrak_sertifika')->name("admin.get_evrak_sertifika");
    });

    // Stok Takip İşlemleri
    Route::controller(StokTakipController::class)->group(function () {

        Route::get('/admin/stok-takip/liste', 'index')->name('admin.stok_takip.index');

        // Sağlık sertifikası düzenleme
        Route::get('/admin/stok-takip/saglik-sertifika/{ss_id}/duzenle', 'ss_edit')->name('admin.stok_takip.ss_edit');
        Route::post('/admin/stok-takip/saglik-sertifika/duzenlendi', 'ss_edited')->name('admin.stok_takip.ss_edited');
    });

    // Admin sistem ayarları
    Route::controller(SettingsController::class)->group(function () {

        Route::get('/admin/sistem-ayarlari/liste', 'index')->name('admin.system_settings.index');
        Route::get('/admin/sistem-ayarlari/duzenle', 'edit')->name('admin.system_settings.edit');
        Route::post('/admin/sistem-ayarlari/duzenlendi', 'edited')->name('admin.system_settings.edited');

        Route::get('/admin/sistem-ayarlari/manual-yedekle', 'manuel_backup')->name('admin.system_settings.manuel_backup');
        Route::post('/admin/sistem-ayarlari/geri-yukleme', 'restore')->name('admin.system_settings.backups.restore');


        Route::get('/admin/sistem-ayarlari/yedekleme/{file}/indir', 'download')->name('admin.system_settings.backups.download');
    });

    // Antrepo Stok Takip İşlemleri
    Route::controller(AntrepoStokTakipController::class)->group(function () {

        Route::get('/admin/antrepo-stok-takip/antrepolar/liste', 'index')->name('admin.antrepo_stok_takip.index');
        Route::get('/admin/antrepo-stok-takip/antrepolar/{id}/detay', 'antrepo_detail')->name('admin.antrepo_stok_takip.detail');
    });

    // Antrepo İşlemleri
    Route::controller(AntrepoController::class)->group(function () {

        Route::get('/admin/antrepolar/liste', 'index')->name('admin.antrepos.index');
        Route::get('/admin/antrepolar/{id}/sil', 'delete')->name('admin.antrepos.delete');

        Route::get('/admin/antrepolar/{id}/düzenle', 'edit')->name('admin.antrepos.edit');
        Route::post('/admin/antrepolar/düzenlendi', 'edited')->name('admin.antrepos.edited');

        Route::get('/admin/antrepolar/ekle', 'create')->name('admin.antrepos.create');
        Route::post('/admin/antrepolar/eklendi', 'created')->name('admin.antrepos.created');
    });

    // Ürün İşlemleri
    Route::controller(UrunController::class)->group(function () {
        Route::get('/admin/ürün/liste', 'index')->name('admin.uruns.index');
        Route::get('/admin/ürün/sil/{id}', 'delete')->name('admin.uruns.delete');

        Route::get('/admin/ürün/düzenle/{id}', 'edit')->name('admin.uruns.edit');
        Route::post('/admin/ürün/düzenlendi', 'edited')->name('admin.uruns.edited');

        Route::get('/admin/ürün/ekle', 'create')->name('admin.uruns.create');
        Route::post('/admin/ürün/eklendi', 'created')->name('admin.uruns.created');
    });

    // Admin Veteriner işlemleri
    Route::controller(VeterinerEController::class)->group(function () {
        Route::get('/admin/veterinerler/liste', 'index')->name('admin.veteriners.index');

        Route::get('/admin/veterinerler/tum-evraklari-onayla', 'confirm_all_evraks')->name('admin.veteriners.tum_evraklari_onayla');

        Route::get('/admin/veterinerler/ekle', 'create')->name('admin.veteriners.create');
        Route::post('/admin/veterinerler/eklendi', 'created')->name('admin.veteriners.created');


        Route::get('/admin/veterinerler/veteriner/evraklar{id}', 'evraks_list')->name('admin.veteriners.veteriner.evraks');
        Route::get('/admin/veterinerler/veteriner/evrak/{type}/{id}/düzenle', 'evrak_edit')->name('admin.veteriners.veteriner.evrak.edit');
        Route::get('/admin/veterinerler/veteriner/evrak/{type}/{id}/detay', 'evrak_detail')->name('admin.veteriners.veteriner.evrak.detail');
        Route::post('/admin/veterinerler/veteriner/evrak/düzenlendi', 'evrak_edited')->name('admin.veteriners.veteriner.evrak.edited');

        Route::get('/admin/veterinerler/veteriner/düzenle/{id}', 'edit')->name('admin.veteriners.veteriner.edit');
        Route::post('/admin/veterinerler/veteriner/düzenlendi', 'edited')->name('admin.veteriners.veteriner.edited');

        Route::get('/admin/veterinerler/veteriner/sil/{id}', 'delete')->name('admin.veteriners.veteriner.delete');
    });

    // Admin Memur işlemleri
    Route::controller(AdminMemurController::class)->group(function () {
        Route::get('/admin/memurlar/liste', 'index')->name('admin.memurs.index');

        Route::get('/admin/memurlar/ekle', 'create')->name('admin.memurs.create');
        Route::post('/admin/memurlar/eklendi', 'created')->name('admin.memurs.created');

        Route::get('/admin/memurlar/memur/düzenle/{id}', 'edit')->name('admin.memurs.memur.edit');
        Route::post('/admin/memurlar/memur/düzenlendi', 'edited')->name('admin.memurs.memur.edited');

        Route::get('/admin/memurlar/memur/sil/{id}', 'delete')->name('admin.memurs.memur.delete');
    });


    // Nöbet işlemleri
    Route::controller(VeterinerNobetController::class)->group(function () {
        Route::get('/admin/veteriner/nöbet/liste', 'index')->name('admin.nobet.veteriner.index');
        Route::post('/admin/veteriner/nöbet/eklendi', 'nobet_created')->name('admin.nobet.veteriner.created');
        Route::post('/admin/veteriner/nöbet/düzenlendi', 'nobet_edited')->name('admin.nobet.veteriner.edited');
        Route::post('/admin/veteriner/nöbet/silindi', 'nobet_deleted')->name('admin.nobet.veteriner.deleted');
    });



    // İzin işlemleri
    Route::controller(VeterinerIzinController::class)->group(function () {
        Route::get('/admin/veteriner/izin/liste', 'index')->name('admin.izin.veteriner.index');
        Route::get('/admin/veteriner/izin/ekle', 'create')->name('admin.izin.veteriner.create');
        Route::post('/admin/veteriner/izin/eklendi', 'created')->name('admin.izin.veteriner.created');
        Route::post('/admin/veteriner/izin/silindi', 'delete')->name('admin.izin.veteriner.delete');
    });
    Route::controller(MemurIzinController::class)->group(function () {
        Route::get('/admin/memur/izin/liste', 'index')->name('admin.izin.memur.index');
        Route::get('/admin/memur/izin/ekle', 'create')->name('admin.izin.memur.create');
        Route::post('/admin/memur/izin/eklendi', 'created')->name('admin.izin.memur.created');
        Route::post('/admin/memur/izin/silindi', 'delete')->name('admin.izin.memur.delete');
    });
});






Route::middleware(['auth', 'role:veteriner'])->group(function () {

    // Veteriner Actions controller
    Route::controller(VeterinerController::class)->group(function () {
        Route::get('/veteriner/anasayfa', 'dashboard')->name('veteriner_dashboard');

        Route::get('/veteriner/profil', 'profile_index')->name('veteriner.profile.index');

        Route::get('/veteriner/profil/düzenle', 'profile_edit')->name('veteriner.profile.edit');
        Route::post('/veteriner/profil/düzenlendi', 'profile_edited')->name('veteriner.profile.edited');

        Route::get('/veteriner/evraklar/liste', 'evraks_index')->name('veteriner.evraks.index');
        Route::get('/veteriner/evraklar/{type}/{id}/detay', 'evrak_index')->name('veteriner.evraks.evrak.index');

        Route::get('veteriner/nöbetler/liste', 'nobets_index')->name('veteriner.nobet.index');
        Route::get('veteriner/izinler/liste', 'izins_index')->name('veteriner.izin.index');

        Route::post('/veteriner/evraks/evrak/onaylandi', 'onaylandi')->name('veteriner.evraks.evrak.onaylandi');
    });
});





Route::middleware(['auth', 'role:memur'])->group(function () {


    // Evrak İşlemleri
    Route::controller(MemurEvrakController::class)->group(function () {
        Route::get('/memur/evrak/liste', 'index')->name('memur.evrak.index');
        Route::get('/memur/evrak/detay/{type}/{id}', 'detail')->name('memur.evrak.detail');

        Route::get('/memur/evrak/düzenle/{type}/{id}', 'edit')->name('memur.evrak.edit');
        Route::post('/memur/evrak/düzenlendi', 'edited')->name('memur.evrak.edited');


        Route::get('/memur/evrak/ekle', 'create')->name('memur.evrak.create');
        Route::post('/memur/evrak/eklendi', 'created')->name('memur.evrak.created');

        Route::post("/memur/evrak/antrepo-sertifika", 'get_evrak_sertifika')->name("memur.get_evrak_sertifika");
    });


    // Stok Takip İşlemleri
    Route::controller(MemurStokTakipController::class)->group(function () {

        Route::get('/memur/stok-takip/liste', 'index')->name('memur.stok_takip.index');
    });



    Route::controller(MemurController::class)->group(function () {
        Route::get('/memur/anasayfa', 'dashboard')->name('memur_dashboard');

        Route::get('/memur/profil', 'profile_index')->name('memur.profile.index');

        Route::get('/memur/profil/düzenle', 'profile_edit')->name('memur.profile.edit');
        Route::post('/memur/profil/düzenlendi', 'profile_edited')->name('memur.profile.edited');

        Route::get('memur/izinler/liste', 'izins_index')->name('memur.izin.index');
    });
});
