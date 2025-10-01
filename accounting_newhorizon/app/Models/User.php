<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'Username',
        'IsActive',
        'email_verified_at',
        'phone',       // ← إضافة
        'address',     // ← إضافة
        'avatar'       // ← إضافة
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'IsActive' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // العلاقة مع الأدوار
    // العلاقة مع الأدوار (Many-to-Many)
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'UserID', 'RoleID')
            ->withPivot('isActive')
            ->withTimestamps();
    }


    // العلاقة مع السجلات
    public function logs()
    {
        return $this->hasMany(Log::class, 'UserID');
    }

    // العلاقة مع سجلات التدقيق
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'ChangedBy');
    }

    // دالة للتحقق من وجود دور معين
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('RoleName', $role);
        }

        return $this->roles->contains('id', $role->id);
    }

    // دالة للتحقق من وجود صلاحية معينة
    // في نموذج User (User Model)
    public function hasPermission($permission)
    {
        try {
            // التحقق من الصلاحيات المباشرة
            if ($this->permissions()->where('PermissionName', $permission)->exists()) {
                return true;
            }

            // التحقق من الصلاحيات عبر الأدوار
            return $this->roles()
                ->whereHas('permissions', function ($query) use ($permission) {
                    $query->where('PermissionName', $permission)
                        ->where('isActive', true);
                })
                ->where('isActive', true)
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
    // دالة للحصول على جميع الصلاحيات (مباشرة + عبر الأدوار)
    public function getAllPermissions()
    {
        $directPermissions = $this->permissions->pluck('PermissionName');
        $rolePermissions = $this->roles->flatMap->permissions->pluck('PermissionName');

        return $directPermissions->merge($rolePermissions)->unique();
    }
    // دالة للحصول على رابط الصورة الشخصية (افتراضي إذا لم توجد)
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        return asset('images/default-avatar.png');
    }
}
