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
        Schema::create('evrak_antrepo_giris', function (Blueprint $table) {
            $table->id();

            $table->string('evrakKayitNo');
            $table->string('vgbOnBildirimNo');
            $table->string('vekaletFirmaKisiAdi');  // firma tablosundan
            $table->string('urunAdi');
            $table->string('gtipNo');
            $table->integer('urunKG');
            $table->string('sevkUlke');
            $table->string('orjinUlke');
            $table->string('aracPlaka');
            $table->string('girisGumruk');
            $table->string('varisAntreposu');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evrak_antrepo_giris');
    }
};
