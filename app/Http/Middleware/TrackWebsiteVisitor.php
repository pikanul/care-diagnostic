<?php

namespace App\Http\Middleware;

use App\Models\GlobalSetting;
use App\Models\VisitorLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TrackWebsiteVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            $this->recordVisit($request);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || ! $response->isSuccessful()) {
            return false;
        }

        if ($request->is('admin*') || $request->is('storage/*') || $request->is('build/*')) {
            return false;
        }

        if (! Schema::hasTable('global_settings') || ! Schema::hasTable('visitor_logs')) {
            return false;
        }

        $settings = GlobalSetting::current()->visitor_tracking_settings ?? [];

        return (bool) ($settings['enabled'] ?? true);
    }

    private function recordVisit(Request $request): void
    {
        $userAgent = (string) $request->userAgent();
        $ipAddress = $request->ip();
        $location = $this->locationFromHeaders($request);

        VisitorLog::create([
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'user_id' => $request->user()?->id,
            'locale' => app()->getLocale(),
            'ip_address' => $ipAddress,
            'hashed_ip' => $ipAddress ? hash('sha256', $ipAddress.config('app.key')) : null,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => '/'.$request->path(),
            'query_string' => $request->getQueryString(),
            'referrer' => $request->headers->get('referer'),
            'landing_page' => $request->session()->get('landing_page', $request->fullUrl()),
            'user_agent' => $userAgent,
            'browser' => $this->browserFromUserAgent($userAgent),
            'platform' => $this->platformFromUserAgent($userAgent),
            'device_type' => $this->deviceTypeFromUserAgent($userAgent),
            'is_bot' => $this->isBot($userAgent),
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_term' => $request->query('utm_term'),
            'utm_content' => $request->query('utm_content'),
            'country' => $location['country'],
            'country_code' => $location['country_code'],
            'region' => $location['region'],
            'city' => $location['city'],
            'timezone' => $location['timezone'],
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'headers' => $this->trackedHeaders($request),
            'last_seen_at' => now(),
        ]);

        if ($request->hasSession() && ! $request->session()->has('landing_page')) {
            $request->session()->put('landing_page', $request->fullUrl());
        }
    }

    /**
     * @return array{country:?string,country_code:?string,region:?string,city:?string,timezone:?string,latitude:?float,longitude:?float}
     */
    private function locationFromHeaders(Request $request): array
    {
        return [
            'country' => $request->headers->get('cf-ipcountry') ?: $request->headers->get('x-country-name'),
            'country_code' => $request->headers->get('cf-ipcountry') ?: $request->headers->get('x-vercel-ip-country'),
            'region' => $request->headers->get('x-vercel-ip-country-region') ?: $request->headers->get('x-region-name'),
            'city' => $request->headers->get('x-vercel-ip-city') ?: $request->headers->get('x-city-name'),
            'timezone' => $request->headers->get('x-vercel-ip-timezone') ?: $request->headers->get('x-timezone'),
            'latitude' => $request->headers->get('x-vercel-ip-latitude') ?: null,
            'longitude' => $request->headers->get('x-vercel-ip-longitude') ?: null,
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function trackedHeaders(Request $request): array
    {
        return [
            'accept_language' => $request->headers->get('accept-language'),
            'cf_ray' => $request->headers->get('cf-ray'),
            'x_forwarded_for' => $request->headers->get('x-forwarded-for'),
            'x_real_ip' => $request->headers->get('x-real-ip'),
        ];
    }

    private function browserFromUserAgent(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Safari/') && ! str_contains($userAgent, 'Chrome/') => 'Safari',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            default => 'Unknown',
        };
    }

    private function platformFromUserAgent(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Macintosh') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown',
        };
    }

    private function deviceTypeFromUserAgent(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone') => 'mobile',
            str_contains($userAgent, 'iPad') || str_contains($userAgent, 'Tablet') => 'tablet',
            default => 'desktop',
        };
    }

    private function isBot(string $userAgent): bool
    {
        return (bool) preg_match('/bot|crawl|spider|slurp|mediapartners|preview|facebookexternalhit/i', $userAgent);
    }
}
