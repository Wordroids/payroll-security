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
        //update the employees table with rank
        Schema::table('sites', function (Blueprint $table) {
            $table->boolean('has_special_ot_hours')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('has_special_ot_hours');
        });
    }
};
