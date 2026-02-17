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
        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->uuid('TransactionID')->primary();
                $table->uuid('WalletID');
                $table->uuid('PatientID');
                $table->uuid('TreatmentDoneID');
                $table->decimal('Amount', 10, 2);
                $table->enum('TransactionType', ['CREDIT', 'DEBIT']);
                $table->text('Description')->nullable();
                $table->decimal('BalanceBefore', 10, 2);
                $table->decimal('BalanceAfter', 10, 2);
                $table->string('Status', 20)->default('COMPLETED')->comment('PENDING, COMPLETED, FAILED, CANCELLED');
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('CreatedOn')->useCurrent();
                
                // Foreign key constraint
                $table->foreign('WalletID')
                      ->references('WalletID')
                      ->on('patient_wallets')
                      ->onDelete('cascade');
                
                // Indexes for better query performance
                $table->index(['WalletID', 'TransactionType']);
                $table->index(['PatientID', 'TransactionType']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
