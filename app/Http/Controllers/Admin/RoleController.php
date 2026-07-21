<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Support\AdminFoundationInstaller;
use App\Support\AdminRoles;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        AdminFoundationInstaller::syncRolesAndPermissions();

        return view('admin.roles.index', $this->viewData());
    }

    public function store(Request $request): RedirectResponse
    {
        AdminFoundationInstaller::syncRolesAndPermissions();

        $validated = $this->validateRole($request);
        $permissionIds = $validated['permission_ids'] ?? [];
        unset($validated['permission_ids']);
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $role = Role::create($validated + ['created_by' => $request->user()->id]);
        $role->permissions()->sync($permissionIds);

        AuditLogger::record('admin_role_created', $role, newValues: $role->load('permissions')->toArray(), request: $request);

        return back()->with('status', 'Role created.');
    }

    public function edit(Role $role): View
    {
        AdminFoundationInstaller::syncRolesAndPermissions();

        return view('admin.roles.index', $this->viewData($role->load('permissions')));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        AdminFoundationInstaller::syncRolesAndPermissions();

        $validated = $this->validateRole($request, $role);
        $permissionIds = $validated['permission_ids'] ?? [];
        unset($validated['permission_ids']);
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        if (array_key_exists($role->slug, AdminRoles::definitions())) {
            $validated['slug'] = $role->slug;
        }
        $validated['is_active'] = $request->boolean('is_active');
        $validated['updated_by'] = $request->user()->id;

        $oldValues = $role->load('permissions')->toArray();
        $role->update($validated);
        $role->permissions()->sync($permissionIds);
        $role->load('permissions');

        AuditLogger::record('admin_role_updated', $role, $oldValues, $role->toArray(), request: $request);

        return redirect()->route('admin.roles.index')->with('status', 'Role updated.');
    }

    public function toggle(Request $request, Role $role): RedirectResponse
    {
        if ($role->slug === AdminRoles::SUPER_ADMIN) {
            return back()->withErrors(['role' => 'Super Admin cannot be deactivated.']);
        }

        $oldValues = $role->toArray();
        $role->update(['is_active' => ! $role->is_active]);

        AuditLogger::record('admin_role_status_changed', $role, $oldValues, $role->toArray(), request: $request);

        return back()->with('status', 'Role status updated.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        if ($role->slug === AdminRoles::SUPER_ADMIN) {
            return back()->withErrors(['role' => 'Super Admin cannot be deleted.']);
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'Remove this role from users before deleting it.']);
        }

        $role->delete();
        AuditLogger::record('admin_role_deleted', $role, request: $request);

        return back()->with('status', 'Role deleted.');
    }

    public function restore(Request $request, int $role): RedirectResponse
    {
        $restoredRole = Role::withTrashed()->findOrFail($role);
        $restoredRole->restore();

        AuditLogger::record('admin_role_restored', $restoredRole, request: $request);

        return back()->with('status', 'Role restored.');
    }

    private function viewData(?Role $editingRole = null): array
    {
        return [
            'roles' => Role::withTrashed()->withCount('users')->with('permissions')->orderBy('display_order')->get(),
            'permissions' => Permission::query()->where('is_active', true)->orderBy('group')->orderBy('display_order')->get()->groupBy('group'),
            'editingRole' => $editingRole,
        ];
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('roles', 'slug')->ignore($role?->id)],
            'description' => ['nullable', 'string'],
            'display_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'permission_ids' => ['required', 'array', 'min:1'],
            'permission_ids.*' => ['integer', Rule::exists('permissions', 'id')->where('is_active', true)],
        ]);
    }
}
