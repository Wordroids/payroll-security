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
        Schema::table('invoice_items', function (Blueprint $table) {
            // Remove days column
            $table->dropColumn('days');

            // Rename number_of_guards to number_of_shifts
            $table->renameColumn('number_of_guards', 'number_of_shifts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->integer('days')->default(1);
            $table->renameColumn('number_of_shifts', 'number_of_guards');
        });
    }
};
