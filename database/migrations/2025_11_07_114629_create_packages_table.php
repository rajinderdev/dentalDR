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
        if (!Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->uuid('PackageID')->default('newid()');
                $table->string('ClinicID', 150)->nullable();
                $table->string('PackageName', 150)->nullable();
                $table->string('PackageCode', 50)->nullable();
                $table->text('Description')->nullable();
                $table->decimal('Price', 10, 2)->nullable();
                $table->string('Interval',255)->nullable();
                $table->decimal('DiscountAmount', 5, 2)->default(0);
                $table->decimal('AdditionAmount', 5, 2)->default(0);
                $table->enum('Status', ['active', 'inactive'])->default('active');
                $table->boolean('IsDeleted')->nullable()->default(false);
                $table->dateTime('CreatedOn')->nullable()->useCurrent();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->nullable()->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();
                $table->primary(['PackageID'], 'pk_package');
            
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('packages')) {
            Schema::dropIfExists('packages');
        }
    }
};
