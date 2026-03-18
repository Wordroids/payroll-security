<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CFormRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'member_no',
        'month',
        'year',
        'total_earnings',
        'employer_epf',
        'employee_epf',
        'etf_contribution',
        'surcharge',
        'cheque_no',
        'bank_name',
        'branch_name',
        'cheque_return_charges',

    ];

    protected $casts = [
        'total_earnings' => 'decimal:2',
        'employer_epf' => 'decimal:2',
        'employee_epf' => 'decimal:2',
        'etf_contribution' => 'decimal:2',
        'surcharge' => 'decimal:2',
        'year' => 'integer',
        'cheque_return_charges' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTotalRemittanceAttribute()
    {
        return $this->etf_contribution + $this->surcharge + $this->cheque_return_charges;
    }
}
