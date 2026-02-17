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
        if (Schema::hasTable('Patient')) {
            Schema::table('Patient', function (Blueprint $table) {
                if(!Schema::hasColumn('Patient', 'Building')) {
                    $table->string('Building')->nullable()->after('Designation');
                }
                if(!Schema::hasColumn('Patient', 'Building_id')) {
                    $table->unsignedBigInteger('Building_id')->nullable()->after('Building');
                }
                if(!Schema::hasColumn('Patient', 'VIP')) {
                    $table->boolean('VIP')->default(false)->after('Notify');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Patient', function (Blueprint $table) {
            $table->dropColumn(['Building', 'Building_id', 'VIP']);
        });
    }
};
