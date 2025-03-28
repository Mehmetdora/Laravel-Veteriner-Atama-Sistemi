<?php

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
        Schema::create('evrak_canli_hayvans', function (Blueprint $table) {
            $table->id();


            $table->string('evrakKayitNo');
            $table->string('vgbOnBildirimNo');
            $table->string('vekaletFirmaKisiAdi');  // firma tablosundan
            $table->string('urunAdi');
            $table->string('gtipNo');
            $table->integer('hayvanSayisi');
            $table->string('sevkUlke');
            $table->string('orjinUlke');
            $table->string('girisGumruk');
            $table->string('cikisGumruk');
            $table->integer('difficulty_coefficient')->default(10);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evrak_canli_hayvans');
    }
};
