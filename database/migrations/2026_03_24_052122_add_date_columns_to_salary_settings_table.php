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
        Schema::table('salary_settings', function (Blueprint $table) {

            if (!Schema::hasColumn('salary_settings', 'applicable_year')) {
                $table->integer('applicable_year')->after('id');
            }
            if (!Schema::hasColumn('salary_settings', 'applicable_month')) {
                $table->integer('applicable_month')->after('applicable_year');
            }
            if (!Schema::hasColumn('salary_settings', 'special_ot_rate')) {
                $table->decimal('special_ot_rate', 10, 2)->default(0)->after('default_attendance_bonus');
            }


            $table->unique(['applicable_year', 'applicable_month']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            //
        });
    }
};
