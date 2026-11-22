<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PRD 0: Manajemen Membership Organisasi (BR-002, BR-003, BR-004, FR-003)
     */
    public function up(): void
    {
        Schema::create('organization_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organization_unit_id')->constrained('organization_units')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->boolean('is_primary')->default(false)->comment('Hanya boleh ada 1 primary membership aktif per user');
            $table->string('status', 20)->default('active')->comment('active, inactive, ended');
            $table->boolean('is_active')->default(true)->index();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'organization_unit_id']);
            $table->index(['user_id', 'is_primary', 'is_active']);
            // $table->index(['organization_unit_id', 'status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_memberships');
    }
};
