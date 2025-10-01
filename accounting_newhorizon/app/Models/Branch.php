<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // توليد الكود: BR-01, BR-02 ...
    protected static function booted()
    {
        static::creating(function (Branch $branch) {
            if (empty($branch->code)) {
                $branch->code = static::nextCode();
            }
        });
    }

    public static function nextCode(): string
    {
        $prefix = 'BR-';
        $padLen = 2; // عدّلها لو تحتاج أكثر
        // طول "BR-" = 3، لذا نبدأ من 4
        $max = DB::table('branches')
            ->selectRaw("MAX(CAST(SUBSTRING(code, 4) AS UNSIGNED)) as max_num")
            ->value('max_num');
        $next = (int)$max + 1;
        return $prefix . str_pad((string)$next, $padLen, '0', STR_PAD_LEFT);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
