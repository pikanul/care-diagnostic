<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WebVitalLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class WebVitalController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (! Schema::hasTable('web_vital_logs')) {
            return response()->json(['ok' => false], 503);
        }

        $validated = $request->validate([
            'metric' => ['required', 'string', 'max:40'],
            'value' => ['required', 'numeric', 'min:0'],
            'rating' => ['nullable', 'string', 'max:40'],
            'path' => ['nullable', 'string', 'max:1024'],
            'url' => ['nullable', 'string', 'max:2048'],
            'metadata' => ['nullable', 'array'],
        ]);

        WebVitalLog::create([
            'metric' => $validated['metric'],
            'value' => $validated['value'],
            'rating' => $validated['rating'] ?? null,
            'path' => $validated['path'] ?? '/'.$request->path(),
            'url' => $validated['url'] ?? $request->fullUrl(),
            'locale' => app()->getLocale(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'device_type' => $this->deviceTypeFromUserAgent((string) $request->userAgent()),
            'browser' => $this->browserFromUserAgent((string) $request->userAgent()),
            'metadata' => $validated['metadata'] ?? null,
        ]);

        return response()->json(['ok' => true]);
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

    private function deviceTypeFromUserAgent(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone') => 'mobile',
            str_contains($userAgent, 'iPad') || str_contains($userAgent, 'Tablet') => 'tablet',
            default => 'desktop',
        };
    }
}
