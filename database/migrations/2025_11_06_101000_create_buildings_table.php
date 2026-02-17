<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('buildings')) {
            Schema::create('buildings', function (Blueprint $table) {
                $table->id();
                $table->string('building_name');
                $table->string('building_code')->nullable();
                $table->text('address1')->nullable();
                $table->text('address2')->nullable();
                $table->string('area')->nullable();
                $table->string('city')->nullable();
                $table->string('state', 20)->nullable();
                $table->string('country')->nullable();
                $table->text('pincode')->nullable();
                $table->boolean('status')->default(true);
                $table->boolean('IsDeleted')->nullable()->default(false);
                $table->dateTime('CreatedOn')->nullable()->useCurrent();
                $table->string('CreatedBy', 50)->nullable();
                $table->dateTime('LastUpdatedOn')->nullable()->useCurrent();
                $table->string('LastUpdatedBy', 50)->nullable();
            });
        }   
    }

    public function down()
    {
        Schema::dropIfExists('buildings');
    }
};
