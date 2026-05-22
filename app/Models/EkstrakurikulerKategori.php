<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EkstrakurikulerKategori extends Model
{
    protected $table = 'tefa_kategori';

    protected $fillable = ['nama_kategori', 'slug'];

    public function ekskul(): HasMany
    {
        return $this->hasMany(Ekstrakurikuler::class, 'kategori_id');
    }
}
