<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbAchievement extends Model
{
    protected $table = 'ppdb_achievements';

    protected $fillable = [
        'application_id',
        'achievement_type',
        'achievement_name',
        'achievement_rank',
        'achievement_level',
        'achievement_year',
        'achievement_organizer',
        'sort_order',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(PpdbApplication::class, 'application_id');
    }
}
