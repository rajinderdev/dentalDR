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
        if (Schema::hasTable('PatientDentalHistoryAttribute') && !Schema::hasColumn('PatientDentalHistoryAttribute', 'DentalHistoryDate')) {
            Schema::table('PatientDentalHistoryAttribute', function (Blueprint $table) {
                $table->dateTime('DentalHistoryDate')->nullable()->after('DentalHistoryAttributeText');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('PatientDentalHistoryAttribute', 'DentalHistoryDate')) {
            Schema::table('PatientDentalHistoryAttribute', function (Blueprint $table) {
                $table->dropColumn('DentalHistoryDate');
            });
        }
    }
};
