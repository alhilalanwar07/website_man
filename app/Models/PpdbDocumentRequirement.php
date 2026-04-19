<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbDocumentRequirement extends Model
{
    protected $table = 'ppdb_document_requirements';

    protected $fillable = [
        'period_id',
        'nama_berkas',
        'keterangan',
        'wajib',
        'is_active',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'wajib' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpdbPeriod::class, 'period_id');
    }
}
