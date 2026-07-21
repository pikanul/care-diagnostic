<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\VisitorLog;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $visitorStats = [
            'total' => 0,
            'today' => 0,
            'active' => 0,
            'topPages' => collect(),
            'topSources' => collect(),
            'devices' => collect(),
            'recentVisitors' => collect(),
        ];

        if (Schema::hasTable('visitor_logs')) {
            $activeWindow = (int) data_get(GlobalSetting::current()->visitor_tracking_settings, 'active_window_minutes', 5);

            $visitorStats = [
                'total' => VisitorLog::where('is_bot', false)->count(),
                'today' => VisitorLog::where('is_bot', false)->whereDate('created_at', today())->count(),
                'active' => VisitorLog::where('is_bot', false)->where('last_seen_at', '>=', now()->subMinutes($activeWindow))->distinct('session_id')->count('session_id'),
                'topPages' => VisitorLog::select('path', DB::raw('count(*) as views'))
                    ->where('is_bot', false)
                    ->groupBy('path')
                    ->orderByDesc('views')
                    ->limit(5)
                    ->get(),
                'topSources' => VisitorLog::select(DB::raw("coalesce(utm_source, referrer, 'Direct') as source"), DB::raw('count(*) as visits'))
                    ->where('is_bot', false)
                    ->groupBy('source')
                    ->orderByDesc('visits')
                    ->limit(5)
                    ->get(),
                'devices' => VisitorLog::select('device_type', DB::raw('count(*) as visits'))
                    ->where('is_bot', false)
                    ->groupBy('device_type')
                    ->orderByDesc('visits')
                    ->get(),
                'recentVisitors' => VisitorLog::where('is_bot', false)->latest()->limit(8)->get(),
            ];
        }

        return view('admin.dashboard', [
            'userCount' => User::count(),
            'recentAuditLogs' => AuditLog::with('user')->latest('created_at')->limit(8)->get(),
            'visitorStats' => $visitorStats,
        ]);
    }
}
