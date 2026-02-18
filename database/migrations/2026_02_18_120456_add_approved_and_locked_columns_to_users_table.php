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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('Approved')->default(true);
            $table->boolean('Locked')->default(false);
        });
        
        if (!Schema::hasColumn('users', 'SecurityQuestion')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('SecurityQuestion')->nullable()->after('locked')->comment('User security question');
            });
        }
        
        if (!Schema::hasColumn('users', 'SecurityAnswer')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('SecurityAnswer')->nullable()->after('SecurityQuestion')->comment('User security answer');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['Approved', 'Locked', 'SecurityQuestion', 'SecurityAnswer']);
        });
    }
};
