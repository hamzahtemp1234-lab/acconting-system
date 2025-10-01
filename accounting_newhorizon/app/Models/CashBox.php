<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CashBox extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'currency_id',
        'account_id',
        'branch_id',
        'keeper_employee_id',
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

    public function keeper() // أمين الصندوق
    {
        return $this->belongsTo(Employee::class, 'keeper_employee_id');
    }

    /*==================== سكوبات مفيدة ====================*/
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /*==================== توليد الكود تلقائياً ====================*/
    protected static function booted()
    {
        static::creating(function (CashBox $cash) {
            if (empty($cash->code)) {
                $cash->code = static::nextCode();
            }
        });
    }

    /**
     * يولد رقم متسلسل على شكل CSH-01, CSH-02 ...
     */
    public static function nextCode(): string
    {
        $prefix = 'CSH-';
        $padLen = 2;

        $max = DB::table('cash_boxes')
            ->selectRaw("MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) AS max_num")
            ->value('max_num');

        $next = (int)$max + 1;
        return $prefix . str_pad((string)$next, $padLen, '0', STR_PAD_LEFT);
    }
}
