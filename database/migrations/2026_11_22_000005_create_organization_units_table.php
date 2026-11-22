<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PRD 0: Master Organisasi & Hierarki Organisasi (BR-001, FR-002)
     */
    public function up(): void
    {
        Schema::create('organization_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('organization_units')
                ->cascadeOnUpdate()
                ->nullOnDelete()
                ->comment('Parent unit id. NULL untuk tingkat negara');
            $table->string('unit_type', 20)->comment('country, province, city, outlet');
            $table->string('code', 50)->unique()->comment('Kode unik unit organisasi');
            $table->string('name', 150);
            
            // Relasi opsional ke master wilayah
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->nullOnDelete();

            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['unit_type', 'is_active']);
            $table->index(['parent_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_units');
    }
};
