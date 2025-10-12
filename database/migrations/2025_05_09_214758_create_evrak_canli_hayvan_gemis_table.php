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
        Schema::create('evrak_canli_hayvan_gemis', function (Blueprint $table) {
            $table->id();

            $table->integer('hayvan_sayisi');
            $table->date('start_date');
            $table->integer('day_count');
            $table->foreignId('kaydeden_kullanici_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evrak_canli_hayvan_gemis');
    }
};
