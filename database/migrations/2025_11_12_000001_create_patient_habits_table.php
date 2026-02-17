<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('patient_habits')) {
            Schema::create('patient_habits', function (Blueprint $table) {
                $table->uuid('PatientHabitID')->default('newid()');
                $table->uuid('PatientID');
                $table->uuid('HabitID');
                $table->text('Notes')->nullable();
                $table->boolean('IsActive')->default(true);
                $table->boolean('IsDeleted')->default(false);
                $table->dateTime('CreatedOn')->useCurrent();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();
                
                 $table->primary(['PatientHabitID'], 'pk_patient_habit');
                
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('patient_habits');
    }
};
