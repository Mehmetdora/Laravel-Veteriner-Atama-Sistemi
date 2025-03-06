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
        Schema::create('evrak_saglik_sertifika', function (Blueprint $table) {
            $table->id();

            $table->foreignId('saglik_sertifika_id')->references('id')->on('saglik_sertifikas')->cascadeOnDelete();
            $table->morphs('evrak'); // evrak_id ve evrak_type için

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evrak_saglik_sertifika');
    }
};
