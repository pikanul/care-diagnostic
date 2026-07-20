<?php

namespace App\Support;

use App\Models\Permission;
use App\Models\Role;

class AdminFoundationInstaller
{
    public static function syncRolesAndPermissions(): void
    {
        $permissions = [];

        foreach (AdminPermissions::definitions() as $slug => $definition) {
            $permissions[$slug] = Permission::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $definition['name'],
                    'group' => $definition['group'],
                    'is_active' => true,
                    'display_order' => array_search($slug, array_keys(AdminPermissions::definitions()), true),
                ]
            );
        }

        foreach (AdminRoles::definitions() as $slug => $definition) {
            $role = Role::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $definition['name'],
                    'is_active' => true,
                    'display_order' => array_search($slug, array_keys(AdminRoles::definitions()), true),
                ]
            );

            $role->permissions()->sync(
                collect($definition['permissions'])
                    ->map(fn (string $permissionSlug) => $permissions[$permissionSlug]->id)
                    ->all()
            );
        }
    }
}
