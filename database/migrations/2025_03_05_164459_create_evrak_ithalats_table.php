<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evrak_ithalats', function (Blueprint $table) {
            $table->id();

            $table->string('evrakKayitNo');
            $table->string('vgbOnBildirimNo');
            $table->string('vekaletFirmaKisiAdi');  // firma tablosundan
            $table->string('urunAdi');
            $table->string('gtipNo');
            $table->decimal('urunKG',10,3); // artık girilen miktar 1111111.111 şeklinde girilebilir
            $table->string('sevkUlke');
            $table->string('orjinUlke');
            $table->string('girisGumruk');
            $table->integer('difficulty_coefficient')->default(20);
            $table->boolean('is_numuneli')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evrak_ithalats');
    }
};
