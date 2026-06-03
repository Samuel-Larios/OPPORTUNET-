<?php

namespace App\Observers;

use App\Models\BlogArticle;
use App\Services\PublicationNewsletterService;

class BlogArticleObserver
{
    public function saved(BlogArticle $article): void
    {
        $isNewPublication = $article->wasRecentlyCreated && $article->statut === 'publie';
        $becamePublished = $article->wasChanged('statut') && $article->statut === 'publie';

        if (! $isNewPublication && ! $becamePublished) {
            return;
        }

        app(PublicationNewsletterService::class)->sendForPublishedArticle($article);
    }
}
