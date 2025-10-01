<?php


// app/Models/SystemSetting.php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class SystemSetting extends Model
{
    use SoftDeletes;


    protected $table = 'system_settings';


    protected $fillable = [
        'company_name',
        'logo',
        'address',
        'phone',
        'email',
        'fiscal_start_month',
        'default_currency_id',
        'decimal_places',
        'extra',
    ];


    protected $casts = [
        'extra' => 'array',
    ];


    public static function instance()
    {
        return static::first();
    }


    // Relation to Currency
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }
}
