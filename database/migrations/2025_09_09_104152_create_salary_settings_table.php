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
        Schema::create('salary_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('default_basic_salary', 10, 2)->default(30000);
            $table->decimal('default_attendance_bonus', 10, 2)->default(5000);
            $table->timestamps();
        });



        DB::table('salary_settings')->insert([
            'default_basic_salary' => 30000,
            'default_attendance_bonus' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_settings');
    }
};
