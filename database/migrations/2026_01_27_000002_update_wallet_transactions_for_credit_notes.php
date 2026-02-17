<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('wallet_transactions', 'TreatmentDoneID')) {
            // MySQL: uuid() becomes CHAR(36). Avoid doctrine/dbal dependency.
            try {
                DB::statement("ALTER TABLE wallet_transactions MODIFY TreatmentDoneID CHAR(36) NULL");
            } catch (\Throwable $e) {
                // If this fails (different DB or already nullable), continue.
            }
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('wallet_transactions', 'FamilyID')) {
                $table->uuid('FamilyID')->nullable()->after('PatientID');
                $table->index('FamilyID', 'idx_wallet_tx_family');
            }

            if (!Schema::hasColumn('wallet_transactions', 'ReceiptID')) {
                $table->uuid('ReceiptID')->nullable()->after('TreatmentDoneID');
                $table->index('ReceiptID', 'idx_wallet_tx_receipt');
            }

            if (!Schema::hasColumn('wallet_transactions', 'ReferenceType')) {
                $table->string('ReferenceType', 50)->nullable()->after('TransactionType');
            }

            if (!Schema::hasColumn('wallet_transactions', 'ReferenceID')) {
                $table->uuid('ReferenceID')->nullable()->after('ReferenceType');
                $table->index(['ReferenceType', 'ReferenceID'], 'idx_wallet_tx_reference');
            }
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_transactions', 'FamilyID')) {
                $table->foreign('FamilyID')
                    ->references('FamilyID')
                    ->on('Family')
                    ->onDelete('set null');
            }

            if (Schema::hasColumn('wallet_transactions', 'ReceiptID')) {
                $table->foreign('ReceiptID')
                    ->references('ReceiptID')
                    ->on('PatientReceipts')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            try { $table->dropForeign(['ReceiptID']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['FamilyID']); } catch (\Throwable $e) {}

            if (Schema::hasColumn('wallet_transactions', 'ReceiptID')) {
                try { $table->dropIndex('idx_wallet_tx_receipt'); } catch (\Throwable $e) {}
                $table->dropColumn('ReceiptID');
            }

            if (Schema::hasColumn('wallet_transactions', 'FamilyID')) {
                try { $table->dropIndex('idx_wallet_tx_family'); } catch (\Throwable $e) {}
                $table->dropColumn('FamilyID');
            }

            if (Schema::hasColumn('wallet_transactions', 'ReferenceID')) {
                try { $table->dropIndex('idx_wallet_tx_reference'); } catch (\Throwable $e) {}
                $table->dropColumn('ReferenceID');
            }

            if (Schema::hasColumn('wallet_transactions', 'ReferenceType')) {
                $table->dropColumn('ReferenceType');
            }

            // Revert TreatmentDoneID to NOT NULL is risky if data exists; leave as-is.
        });
    }
};
