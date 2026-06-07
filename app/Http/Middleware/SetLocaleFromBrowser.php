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
        $queryLocale = $request->query('lang');
        $sessionLocale = $request->session()->get('locale');
        $preferredLocale = in_array($queryLocale, $supportedLocales, true)
            ? $queryLocale
            : (in_array($sessionLocale, $supportedLocales, true)
                ? $sessionLocale
                : $request->getPreferredLanguage($supportedLocales));

        if (in_array($preferredLocale, $supportedLocales, true)) {
            $request->session()->put('locale', $preferredLocale);
        }

        App::setLocale(
            in_array($preferredLocale, $supportedLocales, true)
                ? $preferredLocale
                : config('app.locale')
        );

        return $next($request);
    }
}
