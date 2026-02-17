<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('PatientAllergyAttribute') && !Schema::hasColumn('PatientAllergyAttribute', 'AllergyDate')) {
            Schema::table('PatientAllergyAttribute', function (Blueprint $table) {
                $table->dateTime('AllergyDate')->nullable()->after('AllergyAttributeText');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PatientAllergyAttribute', function (Blueprint $table) {
            $table->dropColumn('AllergyDate');
        });
    }
};
