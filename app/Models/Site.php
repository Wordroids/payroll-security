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
        'has_special_ot_hours',
    ];
    protected $casts = [
        'has_special_ot_hours' => 'boolean',
    ];
    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withPivot('rank');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function rankRates()
    {
        return $this->hasMany(SiteRankRate::class);
    }

    public function getCalculatedSpecialOtRateAttribute()
    {
        if (!$this->has_special_ot_hours) {
            return 0;
        }

        // special OT rate: guard_shift_rate/12*1.5
        $guardShiftRate = $this->guard_shift_rate ?? 0;
        return 200;
    }
}
