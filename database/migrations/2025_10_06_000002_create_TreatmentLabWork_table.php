<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('TreatmentLabWork')) {
            Schema::create('TreatmentLabWork', function (Blueprint $table) {
                $table->uuid('TreatmentLabWorkID')->primary();
                $table->uuid('PatientTreatmentsDoneID');
                $table->uuid('PatientID');
                $table->uuid('ProviderID');
                $table->string('PatientLabID')->nullable();
                $table->string('CreatedBy')->nullable();
                $table->timestamp('CreatedOn')->nullable();
                $table->string('LastUpdatedBy')->nullable();
                $table->timestamp('LastUpdatedOn')->nullable();
                $table->date('LabWorkDate')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('TreatmentLabWork');
    }
};


