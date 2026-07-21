@extends('layouts.admin', ['title' => 'User Management'])

@section('content')
    @php
        $selectedRoles = collect(old('role_ids', $editingUser?->roles->pluck('id')->all() ?? []))->map(fn ($id) => (string) $id)->all();
    @endphp

    <div class="panel" style="margin-bottom:16px;">
        <h2>{{ $editingUser ? 'Edit User' : 'Create User' }}</h2>
        <p class="muted" style="margin-top:4px;">Create admin users and choose which role controls their access.</p>

        <form class="form-grid form-wide" method="POST" action="{{ $editingUser ? route('admin.users.update', $editingUser) : route('admin.users.store') }}">
            @csrf
            @if ($editingUser)
                @method('PUT')
            @endif

            <label>Name
                <input type="text" name="name" value="{{ old('name', $editingUser->name ?? '') }}" required>
            </label>
            <label>Email
                <input type="email" name="email" value="{{ old('email', $editingUser->email ?? '') }}" required>
            </label>
            <label>Password
                <input type="password" name="password" @required(! $editingUser) autocomplete="new-password">
                @if ($editingUser)
                    <span class="muted">Leave blank to keep current password.</span>
                @endif
            </label>
            <label>Confirm Password
                <input type="password" name="password_confirmation" @required(! $editingUser) autocomplete="new-password">
            </label>

            <div class="full-row admin-check-card">
                <strong>Assign Roles</strong>
                <div class="admin-check-grid">
                    @foreach ($roles as $role)
                        <label class="check">
                            <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" @checked(in_array((string) $role->id, $selectedRoles, true))>
                            {{ $role->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editingUser->is_active ?? true))> Active user</label>
            <label class="check"><input type="checkbox" name="two_factor_enabled" value="1" @checked(old('two_factor_enabled', $editingUser->two_factor_enabled ?? false))> Two-factor structure enabled</label>

            <div class="full-row admin-form-actions">
                <button type="submit">{{ $editingUser ? 'Update User' : 'Create User' }}</button>
                @if ($editingUser)
                    <a class="button secondary" href="{{ route('admin.users.index') }}">Cancel Edit</a>
                @endif
            </div>
        </form>
    </div>

    <div class="panel">
        <div class="admin-list-head">
            <div>
                <h2>Users</h2>
                <p class="muted" style="margin:4px 0 0;">Show users, role access, status, and recent login details.</p>
            </div>
        </div>

        <div class="table-list" style="margin-top:16px;">
            @foreach ($users as $user)
                <div class="table-row admin-table-row user-management-row">
                    <div>
                        <strong>{{ $user->name }}</strong><br>
                        <a class="muted" href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                    </div>
                    <div>
                        <div class="admin-pill-row">
                            @forelse ($user->roles as $role)
                                <span class="admin-pill">{{ $role->name }}</span>
                            @empty
                                <span class="muted">No role assigned</span>
                            @endforelse
                        </div>
                        <div class="muted" style="margin-top:8px;">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                            @if ($user->last_login_at)
                                · Last login {{ $user->last_login_at->format('M j, Y g:i A') }}
                            @endif
                        </div>
                    </div>
                    <div class="admin-row-actions">
                        <a class="button" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                            @csrf
                            <button class="secondary" type="submit">{{ $user->is_active ? 'Deactivate' : 'Activate' }}</button>
                        </form>
                        @if (! auth()->user()->is($user))
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user permanently?');">
                                @csrf
                                @method('DELETE')
                                <button class="danger-button" type="submit">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

