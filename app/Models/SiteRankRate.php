<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteRankRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'rank',
        'site_shift_rate',
        'guard_shift_rate'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
