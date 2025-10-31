<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'rank',
        'number_of_guards',
        'days',
        'rate',
        'subtotal'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

  /*  public function employee()
    {
        return $this->belongsTo(Employee::class);
    }*/
}
