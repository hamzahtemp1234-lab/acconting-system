<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'Timestamp',
        'LogLevel',
        'Message',
        'UserID',
        'isActive'
    ];

    protected $casts = [
        'Timestamp' => 'datetime',
        'isActive' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
