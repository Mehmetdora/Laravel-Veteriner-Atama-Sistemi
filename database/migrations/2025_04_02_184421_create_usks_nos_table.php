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
        Schema::create('usks_nos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evrak_antrepo_sertifika_id')->constrained()->onDelete('cascade');
            $table->string('usks_no');
            $table->decimal('miktar',10,3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usks_nos');
    }
};
