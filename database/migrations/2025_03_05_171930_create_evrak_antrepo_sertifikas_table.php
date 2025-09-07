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
        Schema::create('evrak_antrepo_sertifikas', function (Blueprint $table) {
            $table->id();

            $table->string('evrakKayitNo');
            $table->string('vekaletFirmaKisiAdi');  // firma tablosundan
            $table->string('urunAdi');
            $table->string('gtipNo');
            $table->decimal('urunKG',10,3);
            $table->string('orjinUlke');
            $table->string('aracPlaka');
            $table->string('cikisAntrepo');
            $table->integer('difficulty_coefficient')->default(2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evrak_antrepo_sertifikas');
    }
};
