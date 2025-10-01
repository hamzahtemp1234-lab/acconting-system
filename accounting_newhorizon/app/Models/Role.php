<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'RoleName',
        'Description',
        'isActive'
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // العلاقة مع المستخدمين
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'RoleID', 'UserID')
            ->withPivot('isActive')
            ->withTimestamps();
    }

    // العلاقة مع الصلاحيات
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'RoleID', 'PermissionID')
            ->withPivot('isActive')
            ->withTimestamps();
    }

    // دالة للحصول على عدد المستخدمين
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }
}
