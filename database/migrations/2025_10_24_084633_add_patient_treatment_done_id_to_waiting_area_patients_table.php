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
          if (Schema::hasTable('WaitingAreaPatients') && !Schema::hasColumn('WaitingAreaPatients', 'PatientTreatmentDoneID')) {
            Schema::table('WaitingAreaPatients', function (Blueprint $table) {
                 $table->string('PatientTreatmentDoneID',500)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('WaitingAreaPatients', function (Blueprint $table) {
            // $table->dropForeign(['PatientTreatmentDoneID']); // Uncomment if foreign key was added
            $table->dropColumn('PatientTreatmentDoneID');
        });
    }
};
