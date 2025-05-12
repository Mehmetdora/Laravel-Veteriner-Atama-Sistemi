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
        Schema::create('gemi_iznis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('veteriner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evrak_id')->constrained('evrak_canli_hayvan_gemis')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gemi_iznis');
    }
};
