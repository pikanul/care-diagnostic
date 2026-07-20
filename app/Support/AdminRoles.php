<?php

namespace App\Support;

class AdminRoles
{
    public const SUPER_ADMIN = 'super-admin';
    public const ADMIN = 'admin';
    public const CONTENT_MANAGER = 'content-manager';
    public const APPOINTMENT_MANAGER = 'appointment-manager';
    public const LABORATORY_MANAGER = 'laboratory-manager';
    public const MARKETING_MANAGER = 'marketing-manager';

    public static function definitions(): array
    {
        return [
            self::SUPER_ADMIN => [
                'name' => 'Super Admin',
                'permissions' => array_keys(AdminPermissions::definitions()),
            ],
            self::ADMIN => [
                'name' => 'Admin',
                'permissions' => [
                    AdminPermissions::ACCESS_ADMIN,
                    AdminPermissions::MANAGE_PROFILE,
                    AdminPermissions::MANAGE_GLOBAL_SETTINGS,
                    AdminPermissions::MANAGE_NAVIGATION,
                    AdminPermissions::MANAGE_FOOTER,
                    AdminPermissions::MANAGE_CONTENT,
                    AdminPermissions::MANAGE_APPOINTMENTS,
                    AdminPermissions::MANAGE_LABORATORY,
                    AdminPermissions::MANAGE_MARKETING,
                    AdminPermissions::VIEW_AUDIT_LOGS,
                ],
            ],
            self::CONTENT_MANAGER => [
                'name' => 'Content Manager',
                'permissions' => [
                    AdminPermissions::ACCESS_ADMIN,
                    AdminPermissions::MANAGE_PROFILE,
                    AdminPermissions::MANAGE_CONTENT,
                ],
            ],
            self::APPOINTMENT_MANAGER => [
                'name' => 'Appointment Manager',
                'permissions' => [
                    AdminPermissions::ACCESS_ADMIN,
                    AdminPermissions::MANAGE_PROFILE,
                    AdminPermissions::MANAGE_APPOINTMENTS,
                ],
            ],
            self::LABORATORY_MANAGER => [
                'name' => 'Laboratory Manager',
                'permissions' => [
                    AdminPermissions::ACCESS_ADMIN,
                    AdminPermissions::MANAGE_PROFILE,
                    AdminPermissions::MANAGE_LABORATORY,
                ],
            ],
            self::MARKETING_MANAGER => [
                'name' => 'Marketing Manager',
                'permissions' => [
                    AdminPermissions::ACCESS_ADMIN,
                    AdminPermissions::MANAGE_PROFILE,
                    AdminPermissions::MANAGE_MARKETING,
                ],
            ],
        ];
    }
}
