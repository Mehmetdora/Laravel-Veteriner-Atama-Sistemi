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
        Schema::create('evrak_antrepo_varis_dis', function (Blueprint $table) {
            $table->id();


            $table->string('evrakKayitNo');
            $table->string('oncekiVGBOnBildirimNo');
            $table->string('vekaletFirmaKisiAdi');  // firma tablosundan
            $table->string('urunAdi');
            $table->string('gtipNo');
            $table->integer('urunKG');
            $table->integer('difficulty_coefficient')->default(1);
            $table->foreignId('giris_antrepo_id')->constrained('giris_antrepos')->onDelete('cascade');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evrak_antrepo_varis_dis');
    }
};
