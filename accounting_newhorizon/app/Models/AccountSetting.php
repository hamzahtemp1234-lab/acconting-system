<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountSetting extends Model
{
    protected $fillable = [
        'module',
        'key',
        'account_id',
        'value_string',
        'is_active',
        'scope_type',
        'scope_id',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // العلاقات
    public function account()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class);
    }

    // Helpers لعرض اسم النطاق
    public function scopeLabel(): string
    {
        if (!$this->scope_type) return 'عام';
        return match ($this->scope_type) {
            'currency' => 'عملة',
            'customer_category' => 'تصنيف عملاء',
            default => ucfirst($this->scope_type)
        };
    }

    // جلب اسم الكيان المرتبط بالنطاق (للعرض فقط)
    public function scopeName(): ?string
    {
        if (!$this->scope_type || !$this->scope_id) return null;

        try {
            return match ($this->scope_type) {
                'currency'          => optional(\App\Models\Currency::find($this->scope_id))->code,
                'customer_category' => optional(\App\Models\CustomerCategory::find($this->scope_id))->name,
                default             => null
            };
        } catch (\Throwable $e) {
            return null;
        }
    }

    // تعريف ثابت لقائمة الوحدات والمفاتيح الممكنة
    public const MODULE_KEYS = [
        'general' => [
            'rounding_account',
            'fx_gain_account',
            'fx_loss_account',
        ],
        'customers' => [
            'default_customer_account',
        ],
        'suppliers' => [
            'default_supplier_account'
        ],
        'cash' => [
            'default_cash_account',
        ],
        'bank' => [
            'default_bank_account',
        ],
        'fx' => [
            'revaluation_gain_account',
            'revaluation_loss_account',
        ],
    ];

    // الخيارات المسموحة للنطاق
    public const SCOPES = [
        null,
        'currency',
        'customer_category',
    ];
}
