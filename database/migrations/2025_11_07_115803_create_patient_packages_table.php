<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if(!Schema::hasTable('patient_packages')) {
            Schema::create('patient_packages', function (Blueprint $table) {
                $table->uuid('PatientPackageID')->default('newid()');
                $table->uuid('PatientID')->nullable();
                $table->string('ClinicID', 150)->nullable();
                $table->uuid('PackageID')->nullable();
                $table->date('StartDate')->nullable();
                $table->date('EndDate')->nullable();
                $table->decimal('TotalCost', 10, 2)->default(0);
                $table->dateTime('PaymentDate')->nullable();
                $table->decimal('AmountPaid', 10, 2)->default(0);
                $table->string('PaymentMode',255)->nullable();
                $table->string('TransactionReference', 100)->nullable();
                $table->enum('PaymentStatus', ['pending', 'paid'])->default('pending');
                $table->enum('Status', ['active', 'expired', 'cancelled'])->default('active');
                $table->boolean('IsDeleted')->nullable()->default(false);
                $table->dateTime('CreatedOn')->nullable()->useCurrent();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->nullable()->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();
                $table->primary(['PatientPackageID'], 'pk_patient_package');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('patient_packages');
    }
};
