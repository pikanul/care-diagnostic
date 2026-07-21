@extends('layouts.admin', ['title' => $title])

@section('content')
    <section class="panel">
        <h2>{{ $title }}</h2>
        <p class="muted">This secure foundation route is permission-protected and visible only to roles granted this access.</p>

        <div class="admin-check-card" style="margin-top:16px;">
            @if ($title === 'Security Configuration')
                <strong>Security Foundation Ready</strong>
                <div class="admin-pill-row">
                    <span class="admin-pill">Admin auth protected</span>
                    <span class="admin-pill">Login throttling active</span>
                    <span class="admin-pill">Role permissions active</span>
                    <span class="admin-pill">Two-factor fields available</span>
                    <span class="admin-pill">Audit logging active</span>
                </div>
            @elseif ($title === 'API Credentials')
                <strong>API Credential Area Ready</strong>
                <p class="muted" style="margin:0;">Use this protected area for future third-party API keys. Access is controlled by the API Credentials permission.</p>
            @elseif ($title === 'Global Destructive Actions')
                <strong>Destructive Action Area Ready</strong>
                <p class="muted" style="margin:0;">Global destructive actions are isolated behind a dedicated permission. Keep this permission restricted to Super Admin unless intentionally delegated.</p>
            @else
                <strong>Secure Foundation Ready</strong>
                <p class="muted" style="margin:0;">This section is protected by role-based permissions.</p>
            @endif
        </div>
    </section>
@endsection
