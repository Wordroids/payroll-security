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
        Schema::table('employee_site', function (Blueprint $table) {
          $table->string('rank')
                  ->default('CSO')
                  ->after('site_id');
        });

      
        DB::table('employee_site')->update(['rank' => 'CSO']);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_site', function (Blueprint $table) {
                     $table->dropColumn('rank');
        });
    }
};
