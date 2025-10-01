<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SupplierCategory extends Model
{
    use SoftDeletes;

    protected $table = 'suplier_categories';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'account_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function account()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class, 'account_id');
    }

    /* توليد الكود تلقائيًا SUP-01, SUP-02 ... */
    protected static function booted()
    {
        static::creating(function (SupplierCategory $cat) {
            if (empty($cat->code)) {
                $cat->code = static::nextCode();
            }
        });
    }


    public static function nextCode(): string
    {
        $prefix = 'CAT-SUP-';
        $padLen = 2;

        // نفترض أن الكود دائماً بالشكل SUP-XX (من الخانة الخامسة أرقام)
        $max = DB::table('suplier_categories')
            ->selectRaw("MAX(CAST(SUBSTRING(code, 9) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $next = (int)$max + 1;
        return $prefix . str_pad($next, $padLen, '0', STR_PAD_LEFT);
    }

    /* سكوب بسيط */
    public function scopeActive($q)
    {
        return $q->where('is_active', 1);
    }
}
