<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedSalary extends Model
{
    protected $fillable = [
        'position',
        'employee_name',
        'basic_salary',
        'fuel_allowance',
        'transport_allowance',
        'other_allowances',
        'epf_deduction',
        'payee_tax',
        'other_deductions'
    ];

 
    public function getNetPayAttribute()
    {
        $earnings = $this->basic_salary + $this->fuel_allowance + $this->transport_allowance + $this->other_allowances;
        $deductions = $this->epf_deduction + $this->payee_tax + $this->other_deductions;
        return $earnings - $deductions;
    }
}
