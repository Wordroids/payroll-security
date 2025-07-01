<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;
    protected $fillable = [
        'emp_no',
        'name',
        'phone',
        'address',
        'nic',
        'date_of_birth',
        'date_of_hire',
        'rank',
    ];

    protected $appends = ['basic', 'br_allow' , 'special_ot_rate' , 'attendance_bonus'];

    public function getBasicAttribute()
    {
        return $this->attributes['basic'] ?? 17000;
    }

    public function getBrAllowAttribute()
    {
        return $this->attributes['br_allow'] ?? 3500;
    }

    public function getSpecialOtRateAttribute()
    {
        return $this->attributes['special_ot_rate'] ?? 200;
    }

    public function getAttendanceBonusAttribute()
    {
        return $this->attributes['attendance_bonus'] ?? 5000;
    }


    public function sites()
    {
        return $this->belongsToMany(Site::class);
    }

    public function salaryAdvances()
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

      public function meals()
    {
        return $this->hasMany(Meals::class);
    }
       public function uniforms()
    {
        return $this->hasMany(Uniform::class);
    }
}
