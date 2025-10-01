<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Bank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'currency_id',
        'account_id',
        'branch_id',
        'iban',
        'swift',
        'contact_name',
        'phone',
        'address',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*==================== العلاقات ====================*/
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /*==================== سكوبات مفيدة ====================*/
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /*==================== توليد الكود تلقائياً ====================*/
    protected static function booted()
    {
        static::creating(function (Bank $bank) {
            if (empty($bank->code)) {
                $bank->code = static::nextCode();
            }
        });
    }

    /**
     * يولد رقم متسلسل على شكل BNK-01, BNK-02 ...
     */
    public static function nextCode(): string
    {
        $prefix = 'BNK-';
        $padLen = 2;

        // يفترض أن الكود دائماً بالشكل BNK-XX ؛ نأخذ ما بعد "BNK-"
        $max = DB::table('banks')
            ->selectRaw("MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) AS max_num")
            ->value('max_num');

        $next = (int)$max + 1;
        return $prefix . str_pad((string)$next, $padLen, '0', STR_PAD_LEFT);
    }
}
