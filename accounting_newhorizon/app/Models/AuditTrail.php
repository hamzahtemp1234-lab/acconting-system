<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditTrail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'TableName',
        'RecordID',
        'ChangedBy',
        'ChangeDate',
        'ChangeType',
        'Details',
        'isActive'
    ];

    protected $casts = [
        'ChangeDate' => 'datetime',
        'isActive' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // العلاقة مع المستخدم الذي قام بالتغيير
    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'ChangedBy');
    }
}
