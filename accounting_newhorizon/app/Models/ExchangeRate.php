<?php

// app/Models/ExchangeRate.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExchangeRate extends Model
{
    use SoftDeletes;

    protected $table = 'exchange_rates';

    protected $fillable = [
        'currency_id',
        'rate',
        'from_date_exchange',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
