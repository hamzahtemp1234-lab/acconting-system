<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Agent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'tax_id',
        'id_number',
        'phone',
        'mobile',
        'email',
        'address',
        'city',
        'country',
        'currency_id',
        'account_id',
        'commission_rate',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
    ];

    // علاقات
    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }
    public function account()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class, 'account_id');
    }

    // توليد الكود تلقائيًا AGT-01, AGT-02 ...
    protected static function booted()
    {
        static::creating(function (Agent $agent) {
            if (empty($agent->code)) {
                $agent->code = static::nextCode();
            }
        });
    }

    public static function nextCode(): string
    {
        $prefix = 'AGT-';
        $padLen = 2;
        $max = DB::table('agents')
            ->selectRaw("MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) as max_num")
            ->value('max_num');
        $next = (int)$max + 1;
        return $prefix . str_pad((string)$next, $padLen, '0', STR_PAD_LEFT);
    }
}
