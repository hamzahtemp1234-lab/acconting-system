<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCenter extends Model
{
    use SoftDeletes;   // ✅ تفعيل الحذف الناعم

    protected $fillable = [
        'code',
        'name',
        'type_id',
        'parent_id',
        'level',
        'is_group',
        'is_active'
    ];

    public function type()
    {
        return $this->belongsTo(CostCenterType::class, 'type_id');
    }

    public function parent()
    {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CostCenter::class, 'parent_id');
    }
}
