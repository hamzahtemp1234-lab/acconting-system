<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use SoftDeletes;

    protected $table = 'user_roles';
    protected $primaryKey = null; // لأنه مفتاح مركب
    public $incrementing = false; // لأنه مش auto-increment

    protected $fillable = [
        'UserID',
        'RoleID',
        'isActive',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    /**
     * العلاقة مع الدور
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID');
    }
}
