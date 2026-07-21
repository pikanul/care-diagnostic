@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <section class="grid">
        <article class="panel">
            <div class="muted">Admin users</div>
            <h2>{{ $userCount }}</h2>
        </article>

        <article class="panel">
            <div class="muted">Two-factor structure</div>
            <h2>{{ auth()->user()->two_factor_enabled ? 'Enabled' : 'Ready' }}</h2>
        </article>

        <article class="panel">
            <div class="muted">Current account</div>
            <h2>{{ auth()->user()->is_active ? 'Active' : 'Inactive' }}</h2>
        </article>

        <article class="panel">
            <div class="muted">Total visitors</div>
            <h2>{{ number_format($visitorStats['total']) }}</h2>
        </article>

        <article class="panel">
            <div class="muted">Today visitors</div>
            <h2>{{ number_format($visitorStats['today']) }}</h2>
        </article>

        <article class="panel">
            <div class="muted">Real-time visitors</div>
            <h2>{{ number_format($visitorStats['active']) }}</h2>
        </article>
    </section>

    <section class="grid" style="margin-top: 16px;">
        <article class="panel">
            <h2>Top pages</h2>
            <div class="table-list">
                @forelse ($visitorStats['topPages'] as $page)
                    <div class="table-row">
                        <div>{{ $page->path }}</div>
                        <div class="muted">{{ number_format($page->views) }} views</div>
                        <div></div>
                    </div>
                @empty
                    <p class="muted">No visitor data yet.</p>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <h2>Traffic sources</h2>
            <div class="table-list">
                @forelse ($visitorStats['topSources'] as $source)
                    <div class="table-row">
                        <div>{{ \Illuminate\Support\Str::limit($source->source, 35) }}</div>
                        <div class="muted">{{ number_format($source->visits) }} visits</div>
                        <div></div>
                    </div>
                @empty
                    <p class="muted">No source data yet.</p>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <h2>Devices</h2>
            <div class="table-list">
                @forelse ($visitorStats['devices'] as $device)
                    <div class="table-row">
                        <div>{{ ucfirst($device->device_type ?: 'unknown') }}</div>
                        <div class="muted">{{ number_format($device->visits) }} visits</div>
                        <div></div>
                    </div>
                @empty
                    <p class="muted">No device data yet.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="panel" style="margin-top: 16px;">
        <h2>Recent visitors</h2>

        <div class="table-list">
            @forelse ($visitorStats['recentVisitors'] as $visitor)
                <div class="table-row">
                    <div>{{ $visitor->created_at?->format('Y-m-d H:i') }}</div>
                    <div>
                        {{ $visitor->path }}
                        <div class="muted">
                            {{ $visitor->device_type }} · {{ $visitor->browser }} · {{ $visitor->platform }}
                            @if ($visitor->city || $visitor->country_code)
                                · {{ collect([$visitor->city, $visitor->region, $visitor->country_code])->filter()->join(', ') }}
                            @endif
                        </div>
                    </div>
                    <div class="muted">{{ $visitor->ip_address ?? 'Unknown IP' }}</div>
                </div>
            @empty
                <p class="muted">No recent visitor entries yet.</p>
            @endforelse
        </div>
    </section>

    <section class="panel" style="margin-top: 16px;">
        <h2>Recent audit activity</h2>

        <div class="table-list">
            @forelse ($recentAuditLogs as $log)
                <div class="table-row">
                    <div>{{ $log->created_at?->format('Y-m-d H:i') }}</div>
                    <div>{{ $log->event }}</div>
                    <div class="muted">{{ $log->user?->email ?? 'System' }}</div>
                </div>
            @empty
                <p class="muted">No audit entries yet.</p>
            @endforelse
        </div>
    </section>
@endsection
