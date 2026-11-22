<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PRD 0: Closure Table untuk hierarki organisasi turunan dan leluhur (Section 9 Performance)
     */
    public function up(): void
    {
        Schema::create('organization_unit_closures', function (Blueprint $table) {
            $table->foreignId('ancestor_id')->constrained('organization_units')->cascadeOnDelete();
            $table->foreignId('descendant_id')->constrained('organization_units')->cascadeOnDelete();
            $table->unsignedInteger('depth')->default(0)->comment('0 = self, 1 = direct child, 2 = grandchild, dst.');

            $table->primary(['ancestor_id', 'descendant_id']);
            $table->index(['descendant_id', 'depth']);
            $table->index(['ancestor_id', 'depth']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_unit_closures');
    }
};
