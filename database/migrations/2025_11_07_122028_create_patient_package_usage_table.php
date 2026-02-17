<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('patient_package_usage')) {
            Schema::create('patient_package_usage', function (Blueprint $table) {
                $table->uuid('PatientPackageUsageID')->default('newid()');
                $table->string('ClinicID', 150)->nullable();
                $table->string('PatientID', 150)->nullable();
                $table->uuid('PatientPackageID')->nullable();
                $table->uuid('PatientTreatmentDoneID')->nullable();
                $table->uuid('ProviderID')->nullable();
                $table->date('TreatmentDate')->nullable();
                $table->text('Notes')->nullable();
                $table->boolean('IsDeleted')->nullable()->default(false);
                $table->dateTime('CreatedOn')->nullable()->useCurrent();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->nullable()->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();
                $table->primary(['PatientPackageUsageID'], 'pk_patient_package_usage');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('patient_package_usage');
    }
};
