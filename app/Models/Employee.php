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
        'date_of_hire'
    ];

    public function sites()
    {
        return $this->belongsToMany(Site::class);
    }
}
