<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'PermissionName',
        'Description',
        'isActive'
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // العلاقة مع الأدوار
    // العلاقة مع الأدوار
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'PermissionID', 'RoleID')
            ->withPivot('isActive')
            ->withTimestamps();
    }

    // دالة للحصول على عدد الأدوار
    public function getRolesCountAttribute()
    {
        return $this->roles()->count();
    }
}
