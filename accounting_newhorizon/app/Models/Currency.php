<?php
// app/Models/Currency.php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Currency extends Model
{
    use SoftDeletes;


    protected $table = 'currencies';


    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];


    // علاقة مع SystemSetting
    public function systemSettings()
    {
        return $this->hasMany(SystemSetting::class, 'default_currency_id');
    }

    // ✅ علاقة مع ExchangeRate
    public function exchangeRates()
    {
        return $this->hasMany(ExchangeRate::class);
    }
}
