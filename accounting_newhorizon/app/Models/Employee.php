<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'department_id',
        'account_id',
        'phone',
        'email',
    ];

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    public function account()
    {
        // غيّر الموديل إن كان اسمك مختلف (مثلاً Account أو CoaAccount)
        return $this->belongsTo(\App\Models\ChartOfAccount::class, 'account_id');
    }
    // توليد الكود: EMP-001, EMP-002 ...
    protected static function booted()
    {
        static::creating(function (Employee $emp) {
            if (empty($emp->code)) {
                $emp->code = static::nextCode();
            }
        });
    }

    public static function nextCode(): string
    {
        $prefix = 'EMP-';
        $padLen = 3;
        // طول "EMP-" = 4، لذا نبدأ من 5
        $max = DB::table('employees')
            ->selectRaw("MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) as max_num")
            ->value('max_num');
        $next = (int)$max + 1;
        return $prefix . str_pad((string)$next, $padLen, '0', STR_PAD_LEFT);
    }
}
