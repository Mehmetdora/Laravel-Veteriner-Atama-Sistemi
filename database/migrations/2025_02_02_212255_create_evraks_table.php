<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evraks', function (Blueprint $table) {
            $table->id();

            $table->date('tarih');
            $table->string('siraNo');
            $table->string('vgbOnBildirimNo');
            $table->string('vetSaglikSertifikasiNo');
            $table->integer('vekaletFirmaKisiId');  // firma tablosundan
            $table->string('urunAdi');
            $table->string('gtipNo');
            $table->integer('urunKG');
            $table->string('sevkUlke');
            $table->string('orjinUlke');
            $table->string('aracPlaka');
            $table->string('girisGumruk');
            $table->string('cıkısGumruk');
            $table->foreignId('urun_id')->references('id')->on('uruns');
            $table->foreignId('evrak_tur_id')->references('id')->on('evrak_turs');
            $table->foreignId('user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evraks');
    }
};
