<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('patient_medical_insurances')) {
            Schema::create('patient_medical_insurances', function (Blueprint $table) {
                $table->uuid('PatientMedicalInsuranceID')->nullable();
                $table->uuid('PatientID')->nullable();
                $table->string('InsuranceProvider', 255)->nullable();
                $table->string('PolicyNumber', 100)->nullable();
                $table->date('ExpirationDate')->nullable();
                $table->text('Notes')->nullable();
                $table->boolean('IsActive')->default(true);
                $table->boolean('IsDeleted')->default(false);
                $table->dateTime('CreatedOn')->useCurrent();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();
                $table->primary(['PatientMedicalInsuranceID'], 'pk_patient_medical_insurance');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('patient_medical_insurances');
    }
};
