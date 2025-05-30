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
        Schema::create('saglik_sertifikas', function (Blueprint $table) {
            $table->id();

            $table->string('ssn');
            $table->integer('toplam_miktar');
            $table->integer('kalan_miktar');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saglik_sertifikas');
    }
};
