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
        Schema::create('daily_total_workloads', function (Blueprint $table) {
            $table->id();

            $table->date('day');
            $table->integer('total_workload');
            $table->integer('nobet_time');
            $table->integer('day_time');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_total_workloads');
    }
};
