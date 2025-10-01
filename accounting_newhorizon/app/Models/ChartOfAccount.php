<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'description',
        'account_type_id',
        'nature',
        'is_group',
        'level',
        'currency_id',
        'allow_entry',
        'is_default',
        'status',
    ];

    /**
     * العلاقة مع النوع
     */
    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    /**
     * العلاقة مع العملة
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * العلاقة مع الحساب الأب
     */
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    /**
     * العلاقة مع الأبناء
     */
    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
