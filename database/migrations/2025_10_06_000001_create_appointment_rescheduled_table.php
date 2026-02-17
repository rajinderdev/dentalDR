<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('AppointmentRescheduled')){
            Schema::create('AppointmentRescheduled', function (Blueprint $table) {
                $table->uuid('RescheduleID')->primary();
                $table->uuid('OldAppointmentID');
                $table->uuid('NewAppointmentID');
                $table->string('Reason')->nullable();
                $table->timestamp('CreatedOn')->nullable();
                $table->string('CreatedBy')->nullable();

                $table->index('OldAppointmentID');
                $table->index('NewAppointmentID');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('AppointmentRescheduled');
    }
};


