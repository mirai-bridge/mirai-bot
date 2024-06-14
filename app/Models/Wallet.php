<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'balance',
        'pk',
        'network_id'
    ];

    public function network()
    {
        return $this->belongsTo(Network::class);
    }
}
