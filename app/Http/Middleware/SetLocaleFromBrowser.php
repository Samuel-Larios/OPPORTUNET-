<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleFromBrowser
{
    /**
     * Detect the preferred browser language and keep only supported locales.
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = ['fr', 'en'];
        $sessionLocale = $request->session()->get('locale');
        $preferredLocale = in_array($sessionLocale, $supportedLocales, true)
            ? $sessionLocale
            : $request->getPreferredLanguage($supportedLocales);

        App::setLocale(
            in_array($preferredLocale, $supportedLocales, true)
                ? $preferredLocale
                : config('app.locale')
        );

        return $next($request);
    }
}
