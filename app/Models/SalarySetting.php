<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class SalarySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicable_year',
        'applicable_month',
        'default_basic_salary',
        'default_attendance_bonus',
        'special_ot_rate',
    ];

    protected $casts = [
        'applicable_year' => 'integer',
        'applicable_month' => 'integer',
        'default_basic_salary' => 'decimal:2',
        'default_attendance_bonus' => 'decimal:2',
        'special_ot_rate' => 'decimal:2',
    ];


    public static function getSettings($month = null)
    {

        $date = $month ? \Carbon\Carbon::parse($month) : now();


        $settings = self::where(function ($query) use ($date) {
            $query->where('applicable_year', '<', $date->year)
                ->orWhere(function ($q) use ($date) {
                    $q->where('applicable_year', $date->year)
                        ->where('applicable_month', '<=', $date->month);
                });
        })
            ->orderBy('applicable_year', 'desc')
            ->orderBy('applicable_month', 'desc')
            ->first();


        if ($settings) {
            return $settings;
        }


        $cutoffDate = \Carbon\Carbon::create(2026, 1, 1, 0, 0, 0);

        if ($date->lt($cutoffDate)) {

            return new self([
                'default_basic_salary' => 27000,
                'default_attendance_bonus' => 5000,
                'special_ot_rate' => 200,
            ]);
        }


        return new self([
            'default_basic_salary' => 30000,
            'default_attendance_bonus' => 5000,
            'special_ot_rate' => 200,
        ]);
    }
}
