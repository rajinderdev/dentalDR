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
        if (!Schema::hasTable('Events')) {
            Schema::create('Events', function (Blueprint $table) {
                $table->uuid('EventID')->primary();
                $table->text('Description')->nullable();
                $table->dateTime('StartDateTime')->nullable();
                $table->dateTime('EndDateTime')->nullable();
                $table->uuid('CreatedBy')->nullable();
                $table->dateTime('CreatedOn')->useCurrent();
                $table->uuid('LastUpdatedBy')->nullable();
                $table->dateTime('LastUpdatedOn')->useCurrent();
                $table->string('Status', 50)->nullable();
                
                // Optional: Add indexes for better query performance
                $table->index('StartDateTime');
                $table->index('EndDateTime');
                $table->index('Status');
                $table->index('CreatedBy');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Events');
    }
};
