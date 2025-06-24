<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Uniform extends Model
{


     use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'type',
        'quantity',
        'unit_price',
        'total_amount',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
