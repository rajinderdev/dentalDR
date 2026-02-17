<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_wallets', 'FamilyID')) {
                $table->uuid('FamilyID')->nullable()->after('PatientID');
                $table->index('FamilyID', 'idx_patient_wallets_family');
                $table->unique('FamilyID', 'uniq_patient_wallets_family');
                $table->foreign('FamilyID')
                    ->references('FamilyID')
                    ->on('Family')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('patient_wallets', 'Currency')) {
                $table->string('Currency', 3)->default('INR')->after('Balance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patient_wallets', function (Blueprint $table) {
            if (Schema::hasColumn('patient_wallets', 'FamilyID')) {
                // Drop FK if exists
                try { $table->dropForeign(['FamilyID']); } catch (\Throwable $e) {}
                try { $table->dropUnique('uniq_patient_wallets_family'); } catch (\Throwable $e) {}
                try { $table->dropIndex('idx_patient_wallets_family'); } catch (\Throwable $e) {}
                $table->dropColumn('FamilyID');
            }

            if (Schema::hasColumn('patient_wallets', 'Currency')) {
                $table->dropColumn('Currency');
            }
        });
    }
};
