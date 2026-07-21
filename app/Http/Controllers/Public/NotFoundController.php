<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WebsiteErrorLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class NotFoundController extends Controller
{
    public function __invoke(Request $request): Response
    {
        if (Schema::hasTable('website_error_logs')) {
            WebsiteErrorLog::create([
                'status_code' => 404,
                'method' => $request->method(),
                'path' => '/'.$request->path(),
                'url' => $request->fullUrl(),
                'referrer' => $request->headers->get('referer'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'locale' => app()->getLocale(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'metadata' => [
                    'query' => $request->query(),
                ],
            ]);
        }

        return response()->view('errors.404', [], 404);
    }
}
