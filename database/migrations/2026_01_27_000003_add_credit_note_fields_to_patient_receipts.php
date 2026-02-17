<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('PatientReceipts', function (Blueprint $table) {
            if (!Schema::hasColumn('PatientReceipts', 'IsCreditNote')) {
                $table->boolean('IsCreditNote')->default(false)->after('ReceiptNotes');
                $table->index('IsCreditNote', 'idx_patient_receipts_is_credit_note');
            }

            if (!Schema::hasColumn('PatientReceipts', 'WalletAmount')) {
                $table->decimal('WalletAmount', 19, 4)->nullable()->default(0)->after('TreatmentPayment');
            }

            if (!Schema::hasColumn('PatientReceipts', 'IsWalletPayment')) {
                $table->boolean('IsWalletPayment')->default(false)->after('IsCreditNote');
                $table->index('IsWalletPayment', 'idx_patient_receipts_is_wallet_payment');
            }

            if (!Schema::hasColumn('PatientReceipts', 'WalletTransactionID')) {
                $table->uuid('WalletTransactionID')->nullable()->after('IsWalletPayment');
                $table->index('WalletTransactionID', 'idx_patient_receipts_wallet_tx');
            }
        });

        Schema::table('PatientReceipts', function (Blueprint $table) {
            if (Schema::hasColumn('PatientReceipts', 'WalletTransactionID')) {
                $table->foreign('WalletTransactionID')
                    ->references('TransactionID')
                    ->on('wallet_transactions')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('PatientReceipts', function (Blueprint $table) {
            try { $table->dropForeign(['WalletTransactionID']); } catch (\Throwable $e) {}

            if (Schema::hasColumn('PatientReceipts', 'WalletTransactionID')) {
                try { $table->dropIndex('idx_patient_receipts_wallet_tx'); } catch (\Throwable $e) {}
                $table->dropColumn('WalletTransactionID');
            }

            if (Schema::hasColumn('PatientReceipts', 'IsWalletPayment')) {
                try { $table->dropIndex('idx_patient_receipts_is_wallet_payment'); } catch (\Throwable $e) {}
                $table->dropColumn('IsWalletPayment');
            }

            if (Schema::hasColumn('PatientReceipts', 'WalletAmount')) {
                $table->dropColumn('WalletAmount');
            }

            if (Schema::hasColumn('PatientReceipts', 'IsCreditNote')) {
                try { $table->dropIndex('idx_patient_receipts_is_credit_note'); } catch (\Throwable $e) {}
                $table->dropColumn('IsCreditNote');
            }
        });
    }
};
