<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCommentaire extends Model
{
    protected $table = 'blog_commentaires';

    protected $fillable = [
        'article_id',
        'user_id',
        'parent_id',
        'auteur_nom',
        'auteur_email',
        'contenu',
        'ip_address',
        'statut',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(BlogArticle::class, 'article_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function statusLabel(): string
    {
        return (string) __('admin.article_comments.statuses.' . $this->statut);
    }

    public function authorLabel(): string
    {
        return (string) $this->auteur_nom;
    }
}
