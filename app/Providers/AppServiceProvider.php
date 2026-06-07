<?php

namespace App\Providers;

use App\Models\BlogArticle;
use App\Models\Formation;
use App\Models\Opportunite;
use App\Observers\BlogArticleObserver;
use App\Observers\FormationObserver;
use App\Observers\OpportuniteObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        if ((bool) env('APP_FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        RateLimiter::for('auth-login', function (Request $request) {
            $email = strtolower((string) $request->input('email'));

            return [
                Limit::perMinute(5)->by($email . '|' . $request->ip()),
                Limit::perMinute(15)->by($request->ip()),
            ];
        });

        RateLimiter::for('auth-register', fn (Request $request) => [
            Limit::perHour(5)->by($request->ip()),
        ]);

        RateLimiter::for('public-form', fn (Request $request) => [
            Limit::perMinutes(10, 6)->by($request->ip() . '|' . $request->path()),
        ]);

        RateLimiter::for('public-action', fn (Request $request) => [
            Limit::perMinutes(10, 20)->by($request->ip() . '|' . $request->path()),
        ]);

        RateLimiter::for('user-form', function (Request $request) {
            $key = (string) ($request->user()?->getAuthIdentifier() ?? $request->ip());

            return [
                Limit::perMinutes(10, 12)->by($key . '|' . $request->path()),
            ];
        });

        Opportunite::observe(OpportuniteObserver::class);
        Formation::observe(FormationObserver::class);
        BlogArticle::observe(BlogArticleObserver::class);
    }
}
