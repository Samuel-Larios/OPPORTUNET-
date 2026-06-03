<?php

use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocaleFromBrowser::class);
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRoleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            $message = app()->getLocale() === 'fr'
                ? 'Les fichiers envoyes sont trop volumineux. Gardez chaque fichier sous 5 Mo et le total de la candidature sous 64 Mo.'
                : 'The uploaded files are too large. Keep each file under 5 MB and the full application under 64 MB.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 413);
            }

            if (preg_match('~^offres-opportunites/([^/]+)/candidatures$~', trim($request->path(), '/'), $matches) === 1) {
                return redirect()->to(
                    route('offers.show', $matches[1]) . '?upload_error=post_too_large#application-form'
                );
            }

            $previousUrl = url()->previous();

            if (is_string($previousUrl) && $previousUrl !== '' && $previousUrl !== $request->fullUrl()) {
                $separator = str_contains($previousUrl, '?') ? '&' : '?';

                return redirect()->to($previousUrl . $separator . 'upload_error=post_too_large');
            }

            return response()->view('errors.413', status: 413);
        });
    })
    ->create();
