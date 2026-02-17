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
        if (!Schema::hasTable('patient_wallets')) {
            Schema::create('patient_wallets', function (Blueprint $table) {
                $table->uuid('WalletID')->primary();
                $table->uuid('PatientID');
                $table->decimal('Balance', 10, 2)->default(0);
                $table->boolean('IsActive')->default(true);
                $table->boolean('IsDeleted')->default(false);
                $table->dateTime('LastTransactionDate')->nullable();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('CreatedOn')->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->useCurrent();
                
                // Foreign key constraint
                $table->foreign('PatientID')
                      ->references('PatientID')
                      ->on('Patient')
                      ->onDelete('cascade');
                
                // Ensure a patient can have only one wallet
                $table->unique('PatientID', 'unique_patient_wallet');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_wallets');
    }
};
