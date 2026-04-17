<?php

namespace App\Models;

use App\Support\NewsHtmlSanitizer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Berita extends Model
{
    use SoftDeletes;

    protected $table = 'berita';

    protected $fillable = [
        'user_id',
        'kategori_id',
        'judul',
        'slug',
        'konten_html',
        'gambar_thumbnail',
        'status_publikasi',
        'view_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'view_count' => 'integer',
        ];
    }

    protected function kontenHtml(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(NewsHtmlSanitizer::class)->sanitize((string) $value),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBerita::class, 'kategori_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status_publikasi', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
