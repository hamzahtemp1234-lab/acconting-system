<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class Customer extends Model
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
        'category_id',
        'credit_limit',
        'opening_balance',
        'opening_balance_date',
        'payment_terms',
        'preferred_payment_method',
        'is_active',
        'registration_date',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance_date' => 'date',
        'registration_date' => 'date',
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
    ];

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }
    public function account()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class);
    }
    public function category()
    {
        return $this->belongsTo(\App\Models\CustomerCategory::class, 'category_id');
    }
    /* ================= توليد الكود تلقائيًا ================= */
    protected static function booted()
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->code)) {
                $customer->code = static::nextCode();
            }
        });
    }

    /** يرجّع الكود التالي مثل CUS-01, CUS-02 ... */
    public static function nextCode(): string
    {
        $prefix = 'CUS-';
        $padLen = 2; // لو حبيت تخليها CUS-0001 غيّرها إلى 4

        // نجيب أكبر رقم بعد البادئة، ونزود 1
        // ملاحظة: 'CUS-' طولها 4، لذلك SUBSTRING(code, 5)
        $max = DB::table('customers')
            ->selectRaw('MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) as max_num')
            ->value('max_num');

        $next = (int) $max + 1;

        return $prefix . str_pad($next, $padLen, '0', STR_PAD_LEFT);
    }

    /**
     * إصدار مع إعادة المحاولة في حال حصل تداخل (Duplicate) بسبب السباق.
     * استعملها فقط لو تبغى إنشاء العملاء بأعداد كبيرة ومتزامنة.
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
                // لو كود مكرر (unique violation) نعيد المحاولة بكود جديد
                if ($attempts >= 5) {
                    throw $e;
                }
            }
        } while (true);
    }
}
