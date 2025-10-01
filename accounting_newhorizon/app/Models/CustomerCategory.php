<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'account_id', // ✅ أضفناه
    ];

    // علاقة مع العملاء
    public function customers()
    {
        return $this->hasMany(Customer::class, 'category_id');
    }

    // العلاقة مع الدليل المحاسبي
    public function account()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class, 'account_id');
        // غيّر اسم الموديل/المسار لو مختلف عندك
    }
}
