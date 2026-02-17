<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('patient_relations')) {
            Schema::create('patient_relations', function (Blueprint $table) {
                $table->uuid('PatientRelationID')->nullable()->primary();
                $table->uuid('PatientID')->nullable();
                $table->uuid('RelatedPatientID')->nullable();
                $table->string('Relation', 100)->nullable();
                $table->text('Notes')->nullable();
                $table->boolean('IsActive')->default(true);
                $table->boolean('IsDeleted')->default(false);
                $table->dateTime('CreatedOn')->useCurrent();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();
                
                // Foreign key constraints
                $table->foreign('PatientID')
                      ->references('PatientID')
                      ->on('Patient')
                      ->onDelete('cascade');
                      
                $table->foreign('RelatedPatientID')
                      ->references('PatientID')
                      ->on('Patient')
                      ->onDelete('cascade');
                
                // Ensure a patient can't be related to the same patient multiple times with the same relation type
                $table->unique(['PatientID', 'RelatedPatientID'], 'unique_patient_relation');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('patient_relations');
    }
};
