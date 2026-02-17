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
                if(!Schema::hasColumn('Patient', 'Designation')){
                    $table->string('Designation', 255)->nullable()->after('Occupation');
                }
                if(!Schema::hasColumn('Patient', 'Notify')){
                    $table->string('Notify', 255)->nullable()->after('Designation');
                }
                if(!Schema::hasColumn('Patient', 'MarriageAnniversary')){
                    $table->date('MarriageAnniversary')->nullable()->after('DOB');
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
            $table->dropColumn(['Designation', 'Notify', 'MarriageAnniversary']);
        });
    }
};
