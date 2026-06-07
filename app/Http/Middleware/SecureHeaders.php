<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $contentSecurityPolicy = implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "connect-src 'self'",
            "font-src 'self' https://fonts.gstatic.com data:",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "img-src 'self' data: https:",
            "object-src 'none'",
            "script-src 'self' 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
        ]);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $response->headers->set('Content-Security-Policy', $contentSecurityPolicy);
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('Content-Language', app()->getLocale());
        $response->headers->set('Vary', 'Accept-Language');

        if ($this->isSensitiveArea($request)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
            $response->headers->set('Cache-Control', 'no-store, private, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function isSensitiveArea(Request $request): bool
    {
        $routeName = (string) ($request->route()?->getName() ?? '');
        $path = trim($request->path(), '/');

        if (str_starts_with($routeName, 'panel.')) {
            return true;
        }

        if (in_array($routeName, [
            'login',
            'login.store',
            'register',
            'register.store',
            'register.company',
            'register.company.store',
            'register.user',
            'register.user.store',
            'verification.notice',
            'verification.verify',
            'verification.send',
        ], true)) {
            return true;
        }

        return str_starts_with($path, 'admin')
            || $path === 'espace-administration'
            || str_starts_with($path, 'connexion')
            || str_starts_with($path, 'inscription')
            || str_starts_with($path, 'verification-email')
            || str_starts_with($path, 'mon-espace')
            || str_starts_with($path, 'espace-entreprise');
    }
}
