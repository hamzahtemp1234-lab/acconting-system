<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Department extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'code',
        'name',
        'manager_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function manager()
    {
        // يتطلب وجود موديل Employee لاحقًا
        return $this->belongsTo(\App\Models\Employee::class, 'manager_id');
    }
    // app/Models/Department.php

    protected static function booted()
    {
        static::creating(function (Department $dep) {
            // لو الكود فاضي ومعنا branch_id → ولّده
            if (empty($dep->code)) {
                $dep->code = static::nextCode();
            }
        });
    }

    public static function nextCode(): string
    {
        $padLen = 3; // 001, 002, ...
        $max = DB::table('departments')

            ->selectRaw("MAX(CAST(code AS UNSIGNED)) as max_num")
            ->value('max_num');

        $next = (int)$max + 1;
        return str_pad((string)$next, $padLen, '0', STR_PAD_LEFT);
    }
}
