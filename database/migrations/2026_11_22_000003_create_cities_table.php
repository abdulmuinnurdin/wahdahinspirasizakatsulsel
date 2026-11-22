<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PRD 0: Master Wilayah - Master Kota/Kabupaten
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('code', 30)->unique()->comment('Kode Kota/Kabupaten, e.g. 7371 untuk Kota Makassar');
            $table->string('name', 150);
            $table->string('type', 20)->default('kota')->comment('Jenis wilayah: kota / kabupaten');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['province_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
