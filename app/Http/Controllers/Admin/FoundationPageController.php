<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class FoundationPageController extends Controller
{
    public function __invoke(string $section): View
    {
        $titles = [
            'users' => 'User Management',
            'roles' => 'Role Management',
            'backups' => 'Backup and Restore',
            'security' => 'Security Configuration',
            'api-credentials' => 'API Credentials',
            'destructive-actions' => 'Global Destructive Actions',
        ];

        abort_unless(isset($titles[$section]), 404);

        return view('admin.foundation.reserved', [
            'title' => $titles[$section],
        ]);
    }
}
