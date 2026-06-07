<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPublicVisits
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        SiteVisit::query()->create([
            'user_id' => $request->user()?->id,
            'route_name' => $request->route()?->getName(),
            'path' => '/' . ltrim($request->path(), '/'),
            'locale' => app()->getLocale(),
            'visitor_hash' => hash('sha256', implode('|', [
                (string) $request->ip(),
                (string) $request->userAgent(),
                (string) config('app.key'),
            ])),
            'visited_at' => now(),
        ]);

        return $response;
    }

    protected function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->expectsJson()) {
            return false;
        }

        if ($request->ajax() || $request->header('X-Livewire')) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        if (! str_contains($contentType, 'text/html')) {
            return false;
        }

        $routeName = (string) ($request->route()?->getName() ?? '');
        $path = trim($request->path(), '/');

        if (
            str_starts_with($routeName, 'panel.')
            || str_starts_with($path, 'admin')
            || str_starts_with($path, 'mon-espace')
            || str_starts_with($path, 'espace-entreprise')
            || str_starts_with($path, 'livewire')
        ) {
            return false;
        }

        return ! in_array($routeName, [
            'login',
            'register',
            'register.user',
            'dashboard',
            'verification.notice',
        ], true);
    }
}
