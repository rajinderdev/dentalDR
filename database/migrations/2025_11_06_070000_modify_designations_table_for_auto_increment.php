<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    // First, remove the auto_increment attribute
    DB::statement('ALTER TABLE designations MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
    
    // Then drop the primary key
    DB::statement('ALTER TABLE designations DROP PRIMARY KEY');
    
    // Finally, add back the auto_increment and primary key
    DB::statement('ALTER TABLE designations MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
}

public function down(): void
{
    // First, remove the auto_increment attribute
    DB::statement('ALTER TABLE designations MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
    
    // Then drop the primary key
    DB::statement('ALTER TABLE designations DROP PRIMARY KEY');
    
    // Convert back to CHAR(36) and set as primary
    DB::statement('ALTER TABLE designations MODIFY COLUMN id CHAR(36) NOT NULL PRIMARY KEY');
}
};
