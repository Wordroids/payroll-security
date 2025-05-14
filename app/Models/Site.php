<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'contact_person',
        'contact_number',
        'email',
        'address',
        'city',
        'start_date',
        'no_of_guards',
        'no_day_shifts',
        'no_night_shifts',
        'site_shift_rate',
        'guard_shift_rate',
        'has_special_ot_hours',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
