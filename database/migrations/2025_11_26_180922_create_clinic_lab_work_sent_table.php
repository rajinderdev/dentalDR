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
        if (!Schema::hasTable('clinic_lab_work_sent')) {
            Schema::create('clinic_lab_work_sent', function (Blueprint $table) {
                // Make this match the exact type of LabWorkID in clinic_lab_works
                $table->uuid('LabWorkSentID')->primary();
                $table->uuid('LabWorkID');  // This should match the type in clinic_lab_works
                $table->uuid('SentID');
                $table->dateTime('SentDate')->nullable();
                $table->boolean('IsDeleted')->default(false);
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('CreatedOn')->nullable();
                $table->string('LastUpdatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->nullable();

                // Add index first
                $table->index('LabWorkID');

                // Then add foreign key with the same character set and collation
                $table->foreign('LabWorkID')
                    ->references('LabWorkID')
                    ->on('clinic_lab_works')
                    ->onDelete('cascade');
            });
            Schema::dropIfExists('clinic_lab_work_sent');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_lab_work_sent');
    }
};
