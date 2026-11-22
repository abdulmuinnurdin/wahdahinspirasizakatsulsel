<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PRD 0: Manajemen Permission (FR-006)
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()->comment('e.g. donations.view, donations.create, members.view');
            $table->string('name', 100);
            $table->string('group', 50)->nullable()->comment('Modul/kelompok: donations, members, reports, organizations');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
