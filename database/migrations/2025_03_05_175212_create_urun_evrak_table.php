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
        Schema::create('urun_evrak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')->constrained()->onDelete('cascade');
            $table->morphs('evrak'); // Evrak modeli için polymorphic ilişki
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urun_evrak');
    }
};
