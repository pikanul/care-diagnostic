@extends('layouts.public')

@section('content')
    <div class="container page-shell">
        <section class="panel" style="min-height:320px;display:grid;place-items:center;text-align:center;">
            <div>
                <p class="muted" style="margin:0 0 8px;">404</p>
                <h1 style="margin:0 0 10px;">Page not found</h1>
                <p class="muted" style="margin:0 0 18px;">The link may be broken or the page may have moved.</p>
                <a class="action-link primary" href="{{ url('/'.app()->getLocale()) }}">Back to homepage</a>
            </div>
        </section>
    </div>
@endsection
