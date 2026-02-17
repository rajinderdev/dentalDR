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
        if (!Schema::hasColumn('TreatmentTypeHierarchy', 'doctorincentive_percentage')) {
            Schema::table('TreatmentTypeHierarchy', function (Blueprint $table) {
                $table->decimal('doctorincentive_percentage', 5, 2)->default(10)->after('SpecialistTreatmentCost')->comment('Doctor incentive percentage for this treatment type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('TreatmentTypeHierarchy', function (Blueprint $table) {
            $table->dropColumn('doctorincentive_percentage');
        });
    }
};
