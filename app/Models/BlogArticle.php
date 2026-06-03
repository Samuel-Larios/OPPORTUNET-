<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogArticle extends Model
{
    use Bilingual;
    use SoftDeletes;

    protected $table = 'blog_articles';

    protected $fillable = [
        'user_id',
        'categorie_id',
        'titre',
        'titre_fr',
        'titre_en',
        'slug',
        'extrait',
        'extrait_fr',
        'extrait_en',
        'contenu',
        'contenu_fr',
        'contenu_en',
        'image_couverture',
        'image_alt',
        'image_alt_fr',
        'image_alt_en',
        'meta_titre',
        'meta_titre_fr',
        'meta_titre_en',
        'meta_description',
        'meta_description_fr',
        'meta_description_en',
        'tags',
        'statut',
        'publie_le',
        'en_vedette',
        'commentaires_actifs',
        'vues',
        'partages',
        'temps_lecture',
    ];

    protected array $bilingual = [
        'titre',
        'extrait',
        'contenu',
        'image_alt',
        'meta_titre',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'publie_le' => 'datetime',
            'en_vedette' => 'boolean',
            'commentaires_actifs' => 'boolean',
            'tags' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(BlogArticleImage::class)->orderBy('sort_order');
    }

    public function featuredImage(): HasOne
    {
        return $this->hasOne(BlogArticleImage::class)->where('is_featured', true)->orderBy('sort_order');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(BlogCommentaire::class, 'article_id');
    }

    public function primaryImageRecord(): ?BlogArticleImage
    {
        if ($this->relationLoaded('featuredImage') && $this->featuredImage) {
            return $this->featuredImage;
        }

        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            return $this->images->firstWhere('is_featured', true) ?? $this->images->first();
        }

        return $this->featuredImage()->first() ?? $this->images()->first();
    }

    public function primaryImageUrl(): ?string
    {
        $image = $this->primaryImageRecord();

        if ($image) {
            return $image->publicUrl();
        }

        if (! $this->image_couverture) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $this->image_couverture)) {
            return $this->image_couverture;
        }

        if (str_starts_with($this->image_couverture, 'images/') || str_starts_with($this->image_couverture, 'storage/')) {
            return asset($this->image_couverture);
        }

        return asset('storage/' . ltrim((string) $this->image_couverture, '/'));
    }

    public function primaryImageAlt(): string
    {
        $image = $this->primaryImageRecord();

        if ($image) {
            return $image->altText((string) $this->titre);
        }

        return (string) ($this->image_alt ?: $this->titre);
    }
}
