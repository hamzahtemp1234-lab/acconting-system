<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalPeriod extends Model
{
    use SoftDeletes; // ✅ تفعيل الحذف الناعم
    protected $fillable = [
        'fiscal_year_id',
        'period_no',
        'start_date',
        'end_date',
        'is_closed'
    ];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }
}
