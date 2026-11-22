<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PRD 0: Master Wilayah - Master Provinsi
     */
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('code', 20)->unique()->comment('Kode Provinsi, e.g. 73 untuk Sulawesi Selatan');
            $table->string('name', 150);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['country_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
