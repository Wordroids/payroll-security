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


}
