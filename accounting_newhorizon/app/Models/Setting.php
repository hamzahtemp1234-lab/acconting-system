<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'SettingName';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'SettingName',
        'SettingValue',
        'DataType',
        'isActive'
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    protected $dates = ['deleted_at'];
}
