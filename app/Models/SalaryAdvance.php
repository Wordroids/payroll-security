<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    /** @use HasFactory<\Database\Factories\SalaryAdvanceFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'advance_date',
        'reason',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
