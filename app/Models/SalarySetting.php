<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalarySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'default_basic_salary',
        'default_attendance_bonus',
        'special_ot_rate',

    ];
    protected $casts = [
        'default_basic_salary' => 'decimal:2',
        'default_attendance_bonus' => 'decimal:2',
        'special_ot_rate' => 'decimal:2',

    ];

    public static function getSettings()
    {
        return self::firstOrCreate([], [
            'default_basic_salary' => 30000,
            'default_attendance_bonus' => 5000,
            'special_ot_rate' => 200,

        ]);
    }
}
