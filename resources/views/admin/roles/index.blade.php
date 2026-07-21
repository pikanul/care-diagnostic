@extends('layouts.admin', ['title' => 'Role Management'])

@section('content')
    @php
        $selectedPermissions = collect(old('permission_ids', $editingRole?->permissions->pluck('id')->all() ?? []))->map(fn ($id) => (string) $id)->all();
    @endphp

    <div class="panel" style="margin-bottom:16px;">
        <h2>{{ $editingRole ? 'Edit Role' : 'Create Role' }}</h2>
        <p class="muted" style="margin-top:4px;">Choose exactly what this role can access inside the admin panel.</p>

        <form class="form-grid form-wide" method="POST" action="{{ $editingRole ? route('admin.roles.update', $editingRole) : route('admin.roles.store') }}">
            @csrf
            @if ($editingRole)
                @method('PUT')
            @endif

            <label>Role Name
                <input type="text" name="name" value="{{ old('name', $editingRole->name ?? '') }}" required>
            </label>
            <label>Slug
                <input type="text" name="slug" value="{{ old('slug', $editingRole->slug ?? '') }}" placeholder="auto-generated-if-empty">
            </label>
            <label>Display Order
                <input type="number" name="display_order" value="{{ old('display_order', $editingRole->display_order ?? 0) }}" min="0" required>
            </label>
            <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editingRole->is_active ?? true))> Active role</label>
            <label class="full-row">Description
                <textarea name="description" rows="3">{{ old('description', $editingRole->description ?? '') }}</textarea>
            </label>

            <div class="full-row permission-builder">
                @foreach ($permissions as $group => $groupPermissions)
                    <section class="permission-group">
                        <h3>{{ str($group ?: 'general')->headline() }}</h3>
                        <div class="admin-check-grid">
                            @foreach ($groupPermissions as $permission)
                                <label class="check">
                                    <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" @checked(in_array((string) $permission->id, $selectedPermissions, true))>
                                    <span>
                                        {{ $permission->name }}
                                        @if (in_array($permission->slug, [
                                            \App\Support\AdminPermissions::MANAGE_SECURITY,
                                            \App\Support\AdminPermissions::MANAGE_API_CREDENTIALS,
                                            \App\Support\AdminPermissions::GLOBAL_DESTRUCTIVE_ACTIONS,
                                        ], true))
                                            <small>Super Admin sensitive</small>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="full-row admin-form-actions">
                <button type="submit">{{ $editingRole ? 'Update Role Permissions' : 'Create Role' }}</button>
                @if ($editingRole)
                    <a class="button secondary" href="{{ route('admin.roles.index') }}">Cancel Edit</a>
                @endif
            </div>
        </form>
    </div>

    <div class="panel">
        <div class="admin-list-head">
            <div>
                <h2>Roles</h2>
                <p class="muted" style="margin:4px 0 0;">Manage role permissions, activate/deactivate roles, and protect Super Admin access.</p>
            </div>
        </div>

        <div class="table-list" style="margin-top:16px;">
            @foreach ($roles as $role)
                <div class="table-row admin-table-row role-management-row">
                    <div>
                        <strong>{{ $role->name }}</strong><br>
                        <span class="muted">{{ $role->slug }}</span><br>
                        <span class="muted">{{ $role->users_count }} users</span>
                    </div>
                    <div>
                        <div class="admin-pill-row">
                            @foreach ($role->permissions->take(8) as $permission)
                                <span class="admin-pill">{{ $permission->name }}</span>
                            @endforeach
                            @if ($role->permissions->count() > 8)
                                <span class="admin-pill">+{{ $role->permissions->count() - 8 }} more</span>
                            @endif
                        </div>
                        <div class="muted" style="margin-top:8px;">
                            {{ $role->deleted_at ? 'Deleted' : ($role->is_active ? 'Active' : 'Inactive') }}
                        </div>
                    </div>
                    <div class="admin-row-actions">
                        @if (! $role->deleted_at)
                            <a class="button" href="{{ route('admin.roles.edit', $role) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.roles.toggle', $role) }}">
                                @csrf
                                <button class="secondary" type="submit">{{ $role->is_active ? 'Deactivate' : 'Activate' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?');">
                                @csrf
                                @method('DELETE')
                                <button class="danger-button" type="submit">Delete</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.roles.restore', $role->id) }}">
                                @csrf
                                <button class="secondary" type="submit">Restore</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
