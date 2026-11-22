<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PRD 0: Transaksi Donasi & Pencatatan Organisasi Asal Transaksi (BR-007, BR-008, FR-007, Section 8)
     */
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique()->comment('Nomor invoice/transaksi, e.g. INV-001');
            $table->foreignId('organization_unit_id')
                ->constrained('organization_units')
                ->restrictOnDelete()
                ->comment('Organisasi pencatat transaksi');
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('User yang menginput transaksi');
            $table->string('donor_name', 150);
            $table->string('donor_phone', 50)->nullable();
            $table->string('donor_email', 100)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('donation_type', 50)->nullable()->comment('zakat_maal, zakat_fitrah, infaq, sedekah, wakaf');
            $table->string('payment_method', 50)->nullable();
            $table->string('status', 30)->default('confirmed')->comment('pending, confirmed, cancelled');
            $table->text('notes')->nullable();
            $table->dateTime('transaction_date')->useCurrent();
            $table->timestamps();

            $table->index(['organization_unit_id', 'transaction_date']);
            $table->index(['created_by', 'transaction_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
