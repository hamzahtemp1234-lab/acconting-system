<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'account_id',
        'category_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* ================= العلاقات ================= */

    public function account()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class, 'account_id');
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\SupplierCategory::class, 'category_id');
    }

    /* ================= توليد الكود تلقائيًا ================= */
    protected static function booted()
    {
        static::creating(function (Supplier $supplier) {
            if (empty($supplier->code)) {
                $supplier->code = static::nextCode();
            }
        });
    }

    /** يرجّع الكود التالي مثل SUP-01, SUP-02 ... */
    public static function nextCode(): string
    {
        $prefix = 'SUP-';
        $padLen = 2; // لو تبي 4 خانات: اجعلها 4

        // 'SUP-' طولها 4، لذلك SUBSTRING(code, 5)
        $max = DB::table('suppliers')
            ->selectRaw('MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) as max_num')
            ->value('max_num');

        $next = (int) $max + 1;

        return $prefix . str_pad((string)$next, $padLen, '0', STR_PAD_LEFT);
    }

    /**
     * إنشاء مع إعادة المحاولة إذا حدث تعارض "كود مكرر" بسبب السباق.
     */
    public static function createWithAutoCode(array $attributes): self
    {
        $attempts = 0;
        do {
            $attempts++;
            try {
                if (empty($attributes['code'])) {
                    $attributes['code'] = static::nextCode();
                }
                return static::create($attributes);
            } catch (QueryException $e) {
                // لو الكود تضارب مع فهرس unique نعيد المحاولة حتى 5 مرات
                if ($attempts >= 5) {
                    throw $e;
                }
            }
        } while (true);
    }
}
