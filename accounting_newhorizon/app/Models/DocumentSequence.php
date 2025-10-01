<?php
// app/Models/DocumentSequence.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentSequence extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'document_type_id',
        'branch_id',
        'fiscal_year_id',
        'prefix',
        'start_number',
        'current_number',
        'padding',
        'reset_period',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_number' => 'integer',
        'current_number' => 'integer',
        'padding' => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    // مولّد رقم الوثيقة النهائي: PREFIX + padded number
    public function formatNextNumber(): string
    {
        $n = max($this->current_number + 1, $this->start_number);
        return ($this->prefix ?? '') . str_pad((string)$n, $this->padding, '0', STR_PAD_LEFT);
    }
}
