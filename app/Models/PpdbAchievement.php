<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbAchievement extends Model
{
    protected $table = 'ppdb_achievements';

    protected $fillable = [
        'application_id',
        'achievement_name',
        'achievement_rank',
        'achievement_level',
        'sort_order',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(PpdbApplication::class, 'application_id');
    }
}
