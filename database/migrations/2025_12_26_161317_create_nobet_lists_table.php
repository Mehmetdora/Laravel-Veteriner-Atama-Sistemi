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
        Schema::create('nobet_lists', function (Blueprint $table) {
            $table->id();

            $table->date('start_date');
            $table->date('end_date');
            $table->json('list'); // tarih -> vet_id listesi

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nobet_lists');
    }
};
