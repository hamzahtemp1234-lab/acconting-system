<?php
// app/Models/DocumentType.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'module', 'is_active', 'requires_approval', 'notes'];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    public function sequences()
    {
        return $this->hasMany(DocumentSequence::class);
    }
}
