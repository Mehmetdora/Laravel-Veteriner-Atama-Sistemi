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
            $table->decimal('urunKG',10,3);
            $table->string('sevkUlke');
            $table->string('orjinUlke');
            $table->string('aracPlaka');
            $table->string('girisGumruk');
            // Eğer antrepo silinirse antrepo ile ilişkili tüm evraklar da silinmiş olacak
            $table->foreignId('giris_antrepo_id')->constrained('giris_antrepos')->onDelete('cascade');
            $table->integer('difficulty_coefficient')->default(5);

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
