<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'site_id',
        'employee_id',
        'invoice_date',
        'total_amount',
        'description',
        'status',
        'other_charges',
    ];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
