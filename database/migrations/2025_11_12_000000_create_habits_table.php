<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('habits')) {
            Schema::create('habits', function (Blueprint $table) {
                $table->uuid('HabitID')->default('newid()');
                $table->string('Name');
                $table->text('Description')->nullable();
                $table->boolean('IsActive')->default(true);
                $table->boolean('IsDeleted')->nullable()->default(false);
                $table->dateTime('CreatedOn')->nullable()->useCurrent();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->nullable()->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();    
                 $table->primary(['HabitID'], 'pk_habit');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('habits')) {
            Schema::dropIfExists('habits');
        }
    }
};
