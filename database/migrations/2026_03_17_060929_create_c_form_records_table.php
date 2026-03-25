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
        Schema::create('c_form_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('member_no');
            $table->string('month');
            $table->integer('year');
            $table->decimal('total_earnings', 15, 2);
            $table->decimal('employer_epf', 15, 2);
            $table->decimal('employee_epf', 15, 2);
            $table->decimal('etf_contribution', 15, 2)->default(0.00);
            $table->decimal('surcharge', 15, 2)->default(0.00);
            $table->string('cheque_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->decimal('cheque_return_charges', 15, 2)->default(0.00);
            $table->timestamps();
            $table->unique(['employee_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_form_records');
    }
};
