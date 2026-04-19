<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbContactPerson extends Model
{
    protected $table = 'ppdb_contact_persons';

    protected $fillable = [
        'period_id',
        'nama',
        'jabatan',
        'nomor_telepon',
        'nomor_whatsapp',
        'email',
        'is_primary',
        'is_active',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpdbPeriod::class, 'period_id');
    }
}
