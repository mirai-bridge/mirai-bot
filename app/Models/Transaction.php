<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Symfony\Component\CssSelector\Node\HashNode;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pairs',
        'amount',
        'output',
        'revenue',
        'receiver',
        'status',
        'customer_id',
        'dispenser_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'amount' => 'double',
        'output' => 'double',
        'revenue' => 'double',
        'customer_id' => 'integer',
        'dispenser_id' => 'integer',
    ];

    public function hash(): HasOne
    {
        return $this->hasOne(Hash::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function dispenser(): BelongsTo
    {
        return $this->belongsTo(Dispenser::class);
    }
}
