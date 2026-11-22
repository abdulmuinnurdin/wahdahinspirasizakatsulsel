<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PRD 0: Access Level Reference (BR-005, FR-004)
     * Level 1: Seluruh organisasi
     * Level 2: Tingkat Negara
     * Level 3: Tingkat Provinsi
     * Level 4: Tingkat Kota atau Gerai
     */
    public function up(): void
    {
        Schema::create('access_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('level')->unique()->comment('1: All, 2: Country, 3: Province, 4: Local/City/Outlet');
            $table->string('code', 30)->unique()->comment('ALL, COUNTRY, PROVINCE, LOCAL');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_levels');
    }
};
