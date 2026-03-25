<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    /** @use HasFactory<\Database\Factories\SalaryFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month_year',
        'basic_salary',
        'attendance_allowance',
        'ot_earnings',
        'net_salary',
        'calculation_snapshot'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
