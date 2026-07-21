<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminFoundationInstaller;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        AdminFoundationInstaller::syncRolesAndPermissions();

        return view('admin.users.index', [
            'users' => User::query()->with('roles')->latest()->get(),
            'roles' => Role::query()->where('is_active', true)->orderBy('display_order')->get(),
            'editingUser' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        AdminFoundationInstaller::syncRolesAndPermissions();

        $validated = $this->validateUser($request);
        $roleIds = $validated['role_ids'] ?? [];
        unset($validated['role_ids']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');

        $user = User::create($validated);
        $user->roles()->sync($roleIds);

        AuditLogger::record('admin_user_created', $user, newValues: $user->load('roles')->toArray(), request: $request);

        return back()->with('status', 'User created and roles assigned.');
    }

    public function edit(User $user): View
    {
        AdminFoundationInstaller::syncRolesAndPermissions();

        return view('admin.users.index', [
            'users' => User::query()->with('roles')->latest()->get(),
            'roles' => Role::query()->where('is_active', true)->orderBy('display_order')->get(),
            'editingUser' => $user->load('roles'),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        AdminFoundationInstaller::syncRolesAndPermissions();

        $validated = $this->validateUser($request, $user);
        $roleIds = $validated['role_ids'] ?? [];
        unset($validated['role_ids']);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }
        $validated['is_active'] = $request->boolean('is_active');
        $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');

        $oldValues = $user->load('roles')->toArray();
        $user->update($validated);
        $user->roles()->sync($roleIds);
        $user->load('roles');

        AuditLogger::record('admin_user_updated', $user, $oldValues, $user->toArray(), request: $request);

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account.']);
        }

        $oldValues = $user->toArray();
        $user->update(['is_active' => ! $user->is_active]);

        AuditLogger::record('admin_user_status_changed', $user, $oldValues, $user->toArray(), request: $request);

        return back()->with('status', 'User status updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $oldValues = $user->load('roles')->toArray();
        $user->roles()->detach();
        $user->delete();

        AuditLogger::record('admin_user_deleted', null, $oldValues, request: $request);

        return back()->with('status', 'User deleted.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'two_factor_enabled' => ['nullable', 'boolean'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', Rule::exists('roles', 'id')->where('is_active', true)],
        ]);
    }
}
