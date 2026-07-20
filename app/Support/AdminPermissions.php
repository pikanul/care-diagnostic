<?php

namespace App\Support;

class AdminPermissions
{
    public const ACCESS_ADMIN = 'access-admin';
    public const MANAGE_PROFILE = 'manage-profile';
    public const MANAGE_GLOBAL_SETTINGS = 'manage-global-settings';
    public const MANAGE_NAVIGATION = 'manage-navigation';
    public const MANAGE_FOOTER = 'manage-footer';
    public const MANAGE_USERS = 'manage-users';
    public const MANAGE_ROLES = 'manage-roles';
    public const MANAGE_BACKUPS = 'manage-backups';
    public const MANAGE_SECURITY = 'manage-security';
    public const MANAGE_API_CREDENTIALS = 'manage-api-credentials';
    public const GLOBAL_DESTRUCTIVE_ACTIONS = 'global-destructive-actions';
    public const MANAGE_CONTENT = 'manage-content';
    public const MANAGE_APPOINTMENTS = 'manage-appointments';
    public const MANAGE_LABORATORY = 'manage-laboratory';
    public const MANAGE_MARKETING = 'manage-marketing';
    public const VIEW_AUDIT_LOGS = 'view-audit-logs';

    public static function definitions(): array
    {
        return [
            self::ACCESS_ADMIN => ['name' => 'Access Admin', 'group' => 'admin'],
            self::MANAGE_PROFILE => ['name' => 'Manage Profile', 'group' => 'account'],
            self::MANAGE_GLOBAL_SETTINGS => ['name' => 'Manage Global Settings', 'group' => 'settings'],
            self::MANAGE_NAVIGATION => ['name' => 'Manage Navigation', 'group' => 'settings'],
            self::MANAGE_FOOTER => ['name' => 'Manage Footer', 'group' => 'settings'],
            self::MANAGE_USERS => ['name' => 'Manage Users', 'group' => 'super-admin'],
            self::MANAGE_ROLES => ['name' => 'Manage Roles', 'group' => 'super-admin'],
            self::MANAGE_BACKUPS => ['name' => 'Backup and Restore', 'group' => 'super-admin'],
            self::MANAGE_SECURITY => ['name' => 'Security Configuration', 'group' => 'super-admin'],
            self::MANAGE_API_CREDENTIALS => ['name' => 'API Credentials', 'group' => 'super-admin'],
            self::GLOBAL_DESTRUCTIVE_ACTIONS => ['name' => 'Global Destructive Actions', 'group' => 'super-admin'],
            self::MANAGE_CONTENT => ['name' => 'Manage Content', 'group' => 'content'],
            self::MANAGE_APPOINTMENTS => ['name' => 'Manage Appointments', 'group' => 'appointments'],
            self::MANAGE_LABORATORY => ['name' => 'Manage Laboratory', 'group' => 'laboratory'],
            self::MANAGE_MARKETING => ['name' => 'Manage Marketing', 'group' => 'marketing'],
            self::VIEW_AUDIT_LOGS => ['name' => 'View Audit Logs', 'group' => 'security'],
        ];
    }
}
