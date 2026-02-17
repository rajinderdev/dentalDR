<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('package_services')) {
            Schema::create('package_services', function (Blueprint $table) {
                $table->uuid('PackageServiceID')->default('newid()');
                $table->uuid('PackageID')->nullable();
                $table->string('ClinicID', 150);
                $table->uuid('TreatmentTypeID')->nullable();
                $table->text('TreatmentName')->nullable();
                $table->integer('QuantityLimit')->default(1);
                $table->boolean('IsDeleted')->nullable()->default(false);
                $table->dateTime('CreatedOn')->nullable()->useCurrent();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->nullable()->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();
                $table->primary(['PackageServiceID'], 'pk_package_service');
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('package_services')) {
            Schema::dropIfExists('package_services');
        }
    }
};
