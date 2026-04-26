<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalarySettingsProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // to update the very first existing record
        $firstRecord = \App\Models\SalarySetting::first();
        if ($firstRecord) {
            $firstRecord->update([
                'applicable_year' => 2025,
                'applicable_month' => 1,
                'default_basic_salary' => 27000,
                'default_attendance_bonus' => 5000,
                'special_ot_rate' => 150
            ]);
        }

        // New record for 2026 onwards
        \App\Models\SalarySetting::updateOrCreate(
            ['applicable_year' => 2026, 'applicable_month' => 1],
            [
                'default_basic_salary' => 30000,
                'default_attendance_bonus' => 5000,
                'special_ot_rate' => 150
            ]
        );
    }
}
