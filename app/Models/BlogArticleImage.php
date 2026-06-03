<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogArticleImage extends Model
{
    use Bilingual;

    protected $fillable = [
        'blog_article_id',
        'image_path',
        'alt',
        'alt_fr',
        'alt_en',
        'is_featured',
        'sort_order',
    ];

    protected array $bilingual = [
        'alt',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(BlogArticle::class, 'blog_article_id');
    }

    public function publicUrl(): string
    {
        if (preg_match('/^https?:\/\//i', $this->image_path)) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, 'images/') || str_starts_with($this->image_path, 'storage/')) {
            return asset($this->image_path);
        }

        return asset('storage/' . ltrim($this->image_path, '/'));
    }

    public function altText(string $fallback = ''): string
    {
        return (string) ($this->alt ?: $fallback);
    }
}
