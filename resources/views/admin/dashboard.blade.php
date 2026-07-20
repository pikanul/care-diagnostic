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
