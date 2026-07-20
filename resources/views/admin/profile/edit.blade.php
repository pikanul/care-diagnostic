@extends('layouts.admin', ['title' => 'Profile'])

@section('content')
    <section class="grid">
        <article class="panel">
            <h2>Profile details</h2>

            <form class="form-grid" method="POST" action="{{ route('admin.profile.update') }}">
                @csrf
                @method('PUT')

                <label>
                    Name
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </label>

                <label>
                    Email
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>

                <button type="submit">Update Profile</button>
            </form>
        </article>

        <article class="panel">
            <h2>Change password</h2>

            <form class="form-grid" method="POST" action="{{ route('admin.profile.password') }}">
                @csrf
                @method('PUT')

                <label>
                    Current password
                    <input type="password" name="current_password" autocomplete="current-password" required>
                </label>

                <label>
                    New password
                    <input type="password" name="password" autocomplete="new-password" required>
                </label>

                <label>
                    Confirm password
                    <input type="password" name="password_confirmation" autocomplete="new-password" required>
                </label>

                <button type="submit">Change Password</button>
            </form>
        </article>

        <article class="panel">
            <h2>Security</h2>
            <p class="muted">Two-factor authentication database fields are ready for a future authenticator setup.</p>
            <p>Status: {{ $user->two_factor_enabled ? 'Enabled' : 'Not enabled' }}</p>
        </article>
    </section>
@endsection
