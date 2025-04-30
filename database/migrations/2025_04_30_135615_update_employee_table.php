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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('rank')->after('date_of_hire')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //remove the rank column from employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('rank');
        });
    }
};
