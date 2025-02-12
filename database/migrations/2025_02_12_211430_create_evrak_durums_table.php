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
        Schema::create('evrak_durums', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evrak_id')->references('id')->on('evraks')->cascadeOnDelete();
            $table->string('evrak_durum')
            ->default('Onaylanacak')
            ->comment('Veterinerin evrağı atanadıktan sonra evrak durumu bilgisi, onaylanacak->beklemede->onaylandı ');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evrak_durums');
    }
};
