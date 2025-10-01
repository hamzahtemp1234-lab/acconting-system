<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountType extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'name',
        'nature',
        'is_active',
    ];
}
