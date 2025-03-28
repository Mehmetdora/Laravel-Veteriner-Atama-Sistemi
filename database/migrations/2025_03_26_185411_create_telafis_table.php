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
        Schema::create('telafis', function (Blueprint $table) {
            $table->id();


            $table->foreignId('workload_id')->references('id')->on('work_loads')->cascadeOnDelete();
            $table->date('startDate');
            $table->date('endDate');
            $table->integer('kalan_telafi_gunleri');
            $table->integer('gunluk_is_yuku');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telafis');
    }
};
