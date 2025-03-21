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
        Schema::create('work_loads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vet_id')->constrained('users')->onDelete('cascade');
            $table->integer('month_workload')->default(0);
            $table->integer('year_workload')->default(0);
            $table->integer('total_workload')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_loads');
    }
};
