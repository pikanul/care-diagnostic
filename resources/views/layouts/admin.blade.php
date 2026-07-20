<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Panel' }} | {{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --sidebar: #14213d;
            --sidebar-soft: #1f3157;
            --panel: #ffffff;
            --text: #172033;
            --muted: #667085;
            --line: #d9e1ec;
            --accent: #0f766e;
            --danger: #b42318;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        a { color: inherit; }

        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 270px minmax(0, 1fr);
        }

        .sidebar {
            background: var(--sidebar);
            color: #fff;
            padding: 22px 18px;
        }

        .brand {
            display: block;
            font-weight: 700;
            line-height: 1.35;
            text-decoration: none;
            margin-bottom: 22px;
        }

        .nav {
            display: grid;
            gap: 6px;
        }

        .nav a,
        .logout-button {
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border: 0;
            border-radius: 8px;
            color: #dbe7ff;
            background: transparent;
            text-align: left;
            text-decoration: none;
            font: inherit;
            cursor: pointer;
        }

        .nav a.active,
        .nav a:hover,
        .logout-button:hover {
            background: var(--sidebar-soft);
            color: #fff;
        }

        .main {
            min-width: 0;
            padding: 24px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0;
            font-size: 26px;
            line-height: 1.2;
        }

        .user-meta,
        .muted {
            color: var(--muted);
            font-size: 14px;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .form-grid {
            display: grid;
            gap: 14px;
            max-width: 720px;
        }

        label {
            display: grid;
            gap: 6px;
            font-weight: 600;
        }

        input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 11px 12px;
            font: inherit;
        }

        button,
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            padding: 11px 14px;
            background: var(--accent);
            color: #fff;
            font: inherit;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .danger { color: var(--danger); }

        .alert {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .table-list {
            display: grid;
            gap: 10px;
        }

        .table-row {
            display: grid;
            grid-template-columns: 160px minmax(0, 1fr) 160px;
            gap: 12px;
            border-bottom: 1px solid var(--line);
            padding-bottom: 10px;
        }

        @media (max-width: 900px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }

            .nav {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .table-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .main {
                padding: 18px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .nav {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('admin.dashboard') }}">
                {{ config('app.name') }}<br>Admin
            </a>

            <nav class="nav" aria-label="Admin navigation">
                <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                <a href="{{ route('admin.profile.edit') }}" @class(['active' => request()->routeIs('admin.profile.*')])>Profile</a>

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_GLOBAL_SETTINGS))
                    <a href="{{ route('admin.global-settings.edit') }}" @class(['active' => request()->routeIs('admin.global-settings.*')])>Global Settings</a>
                @endif

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_NAVIGATION))
                    <a href="{{ route('admin.navigation.index') }}" @class(['active' => request()->routeIs('admin.navigation.*')])>Navigation</a>
                @endif

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_FOOTER))
                    <a href="{{ route('admin.footer-sections.index') }}" @class(['active' => request()->routeIs('admin.footer-sections.*')])>Footer Sections</a>
                    <a href="{{ route('admin.footer-links.index') }}" @class(['active' => request()->routeIs('admin.footer-links.*')])>Footer Links</a>
                @endif

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_CONTENT))
                    <a href="{{ route('admin.homepage.index') }}" @class(['active' => request()->routeIs('admin.homepage.*')])>Homepage Builder</a>
                @endif

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_USERS))
                    <a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.users.*')])>Users</a>
                @endif

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_ROLES))
                    <a href="{{ route('admin.roles.index') }}" @class(['active' => request()->routeIs('admin.roles.*')])>Roles</a>
                @endif

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_BACKUPS))
                    <a href="{{ route('admin.backups.index') }}" @class(['active' => request()->routeIs('admin.backups.*')])>Backup</a>
                @endif

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_SECURITY))
                    <a href="{{ route('admin.security.index') }}" @class(['active' => request()->routeIs('admin.security.*')])>Security</a>
                @endif

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_API_CREDENTIALS))
                    <a href="{{ route('admin.api-credentials.index') }}" @class(['active' => request()->routeIs('admin.api-credentials.*')])>API Credentials</a>
                @endif

                @if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::GLOBAL_DESTRUCTIVE_ACTIONS))
                    <a href="{{ route('admin.destructive-actions.index') }}" @class(['active' => request()->routeIs('admin.destructive-actions.*')])>Destructive Actions</a>
                @endif

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="main">
            <header class="topbar">
                <div>
                    <h1>{{ $title ?? 'Admin Panel' }}</h1>
                    <div class="user-meta">{{ auth()->user()?->name }} · {{ auth()->user()?->roles->pluck('name')->join(', ') }}</div>
                </div>
            </header>

            @if (session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert danger">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
