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
        Schema::create('arac_plaka_kgs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('evrak_ithalat_id')->constrained('evrak_ithalats')->onDelete('cascade');
            $table->string('arac_plaka');
            $table->integer('miktar');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arac_plaka_kgs');
    }
};
