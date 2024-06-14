<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispenser extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'balance',
        'pk',
        'used',
        'ticker_id',
        'customer_id'
    ];
}
