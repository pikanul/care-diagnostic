@extends('layouts.admin', ['title' => $title])

@section('content')
    <section class="panel">
        <h2>{{ $title }}</h2>
        <p class="muted">
            This secure foundation route is reserved for Super Admin access.
            The full management module has not been built in this phase.
        </p>
    </section>
@endsection
