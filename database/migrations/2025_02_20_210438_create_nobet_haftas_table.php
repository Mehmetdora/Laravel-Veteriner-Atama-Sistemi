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
        Schema::create('nobet_haftas', function (Blueprint $table) {
            $table->id();

            $table->string('weekName')->unique();
            $table->date('startOfWeek');
            $table->date('endOfWeek');
            $table->json('sun');
            $table->json('mon');
            $table->json('tue');
            $table->json('wed');
            $table->json('thu');
            $table->json('fri');
            $table->json('sat');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nobet_haftas');
    }
};
