<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    //protected $primaryKey = 'reservation_code';
    public $incrementing = false; // por el id de tipo string

    protected $fillable = [
        'id',
        'full_name',
        'email',
        'number_of_guests',
        'status',
        'reservation_code',
        'date',
        'amount',
        'discount',
        'total_amount',
        'payment_type'
    ];
}
