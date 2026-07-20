<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported application locales.
     */
    public const SUPPORTED_LOCALES = ['en', 'bn'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            abort(404);
        }

        session(['locale' => $locale]);

        app()->setLocale($locale);
        config(['app.locale' => $locale]);
        view()->share('currentLocale', $locale);
        view()->share('supportedLocales', self::SUPPORTED_LOCALES);

        return $next($request);
    }
}
