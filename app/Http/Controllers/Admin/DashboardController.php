<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\VisitorLog;
use App\Models\GlobalSetting;
use Illuminate\Support\Carbon;
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
            'chartData' => [
                'daily' => [],
                'hourly' => [],
                'devices' => [],
                'sources' => [],
            ],
        ];

        if (Schema::hasTable('visitor_logs')) {
            $activeWindow = (int) data_get(GlobalSetting::current()->visitor_tracking_settings, 'active_window_minutes', 5);
            $dailyRows = VisitorLog::select(DB::raw('date(created_at) as visit_date'), DB::raw('count(*) as visits'))
                ->where('is_bot', false)
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('visit_date')
                ->orderBy('visit_date')
                ->pluck('visits', 'visit_date');
            $hourlyRows = VisitorLog::select(DB::raw('hour(created_at) as visit_hour'), DB::raw('count(*) as visits'))
                ->where('is_bot', false)
                ->whereDate('created_at', today())
                ->groupBy('visit_hour')
                ->orderBy('visit_hour')
                ->pluck('visits', 'visit_hour');
            $deviceRows = VisitorLog::select('device_type', DB::raw('count(*) as visits'))
                ->where('is_bot', false)
                ->groupBy('device_type')
                ->orderByDesc('visits')
                ->get();
            $sourceRows = VisitorLog::select(DB::raw("coalesce(utm_source, referrer, 'Direct') as source"), DB::raw('count(*) as visits'))
                ->where('is_bot', false)
                ->groupBy('source')
                ->orderByDesc('visits')
                ->limit(6)
                ->get();

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
                'chartData' => [
                    'daily' => collect(range(6, 0))->map(function (int $daysAgo) use ($dailyRows): array {
                        $date = now()->subDays($daysAgo);
                        $key = $date->toDateString();

                        return [
                            'label' => $date->format('M j'),
                            'value' => (int) ($dailyRows[$key] ?? 0),
                            'date' => $key,
                        ];
                    })->values()->all(),
                    'hourly' => collect(range(0, 23))->map(function (int $hour) use ($hourlyRows): array {
                        return [
                            'label' => Carbon::createFromTime($hour)->format('g A'),
                            'value' => (int) ($hourlyRows[$hour] ?? 0),
                        ];
                    })->values()->all(),
                    'devices' => $deviceRows->map(fn ($row): array => [
                        'label' => ucfirst($row->device_type ?: 'unknown'),
                        'value' => (int) $row->visits,
                    ])->values()->all(),
                    'sources' => $sourceRows->map(fn ($row): array => [
                        'label' => (string) $row->source,
                        'value' => (int) $row->visits,
                    ])->values()->all(),
                ],
            ];
        }

        return view('admin.dashboard', [
            'userCount' => User::count(),
            'recentAuditLogs' => AuditLog::with('user')->latest('created_at')->limit(8)->get(),
            'visitorStats' => $visitorStats,
        ]);
    }
}
