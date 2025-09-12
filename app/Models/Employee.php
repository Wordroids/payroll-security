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
        'basic_salary',
        'attendance_bonus',
    ];

    protected $appends = ['basic', 'attendance_bonus'];

    public function getBasicAttribute()
    {
        return $this->attributes['basic_salary'] ?? SalarySetting::getSettings()->default_basic_salary;
    }

    public function getAttendanceBonusAttribute()
    {
        return $this->attributes['attendance_bonus'] ?? SalarySetting::getSettings()->default_attendance_bonus;
    }


    public function sites()
    {
        return $this->belongsToMany(Site::class) ->withPivot('rank');
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
