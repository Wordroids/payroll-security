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
        'amount',
        'notes',

    ];
    protected $casts = [
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
