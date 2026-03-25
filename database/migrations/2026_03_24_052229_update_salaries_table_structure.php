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
        Schema::table('salaries', function (Blueprint $table) {
            $table->foreignId('employee_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('month_year')->after('employee_id'); 
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('attendance_allowance', 10, 2);
            $table->decimal('ot_earnings', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->json('calculation_snapshot')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            //
        });
    }
};
