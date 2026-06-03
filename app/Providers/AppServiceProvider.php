<?php

namespace App\Providers;

use App\Models\BlogArticle;
use App\Models\Formation;
use App\Models\Opportunite;
use App\Observers\BlogArticleObserver;
use App\Observers\FormationObserver;
use App\Observers\OpportuniteObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Opportunite::observe(OpportuniteObserver::class);
        Formation::observe(FormationObserver::class);
        BlogArticle::observe(BlogArticleObserver::class);
    }
}
