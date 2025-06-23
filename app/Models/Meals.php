<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meals extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'date',
        'meal_items',
        'total_amount',
        'notes',

    ];
    protected $casts = [
        'meal_items' => 'array',
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
