<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbImportantDate extends Model
{
    protected $table = 'ppdb_important_dates';

    protected $fillable = [
        'period_id',
        'label',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'is_active',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpdbPeriod::class, 'period_id');
    }
}
