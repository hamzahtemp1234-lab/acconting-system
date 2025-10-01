<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'role_permissions';
    protected $primaryKey = ['RoleID', 'PermissionID'];
    public $incrementing = false;

    protected $fillable = [
        'RoleID',
        'PermissionID',
        'isActive'
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // العلاقة مع الدور
    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID');
    }

    // العلاقة مع الصلاحية
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'PermissionID');
    }
}
