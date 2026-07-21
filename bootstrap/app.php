<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\TrackWebsiteVisitor::class,
        ]);

        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\EnsureAdminIsAuthenticated::class,
            'admin.active' => \App\Http\Middleware\EnsureAdminIsActive::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'permission' => \App\Http\Middleware\EnsureUserHasPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if ($request->is('admin/*')) {
                return back()->withErrors([
                    'upload' => 'The uploaded files are larger than the server limit. Restart the local Laravel server with the larger upload command shown in the completion notes, then try again.',
                ]);
            }

            return null;
        });
    })->create();
