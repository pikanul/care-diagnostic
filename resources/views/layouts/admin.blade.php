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

        .nav-group {
            display: grid;
            gap: 4px;
        }

        .nav-group-label {
            margin: 8px 10px 2px;
            color: #9fb4d8;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .nav-submenu {
            display: grid;
            gap: 3px;
            margin-left: 10px;
            padding-left: 10px;
            border-left: 1px solid rgba(219,231,255,.2);
        }

        .nav .nav-submenu a {
            padding: 8px 10px;
            border-radius: 7px;
            color: #c7d7f3;
            font-size: 13px;
            line-height: 1.25;
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

        .form-grid.form-wide {
            max-width: none;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: start;
        }

        .form-grid.form-wide > button,
        .form-grid.form-wide > .full-row {
            grid-column: 1 / -1;
        }

        label {
            display: grid;
            gap: 6px;
            font-weight: 600;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 11px 12px;
            font: inherit;
        }

        textarea {
            resize: vertical;
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

        .admin-list-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .admin-list-head h2 {
            margin: 0;
        }

        .admin-table-row {
            grid-template-columns: 120px minmax(0, 1fr) auto;
            align-items: center;
            gap: 16px;
            padding: 14px 0;
        }

        .admin-order-input {
            max-width: 88px;
            text-align: center;
        }

        .admin-row-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .admin-row-actions form {
            margin: 0;
            display: inline-flex;
        }

        .admin-form-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .admin-check-card,
        .permission-builder {
            display: grid;
            gap: 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 14px;
            background: #fbfdff;
        }

        .admin-check-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
        }

        .check input {
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
        }

        .permission-group {
            display: grid;
            gap: 10px;
            border-bottom: 1px solid var(--line);
            padding-bottom: 14px;
        }

        .permission-group:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .permission-group h3 {
            margin: 0;
            font-size: 16px;
        }

        .permission-group small {
            display: block;
            color: #b42318;
            font-size: 11px;
            font-weight: 800;
        }

        .admin-pill-row {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .admin-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 9px;
            background: #e7eef8;
            color: #12325f;
            font-size: 12px;
            font-weight: 800;
        }

        .user-management-row,
        .role-management-row {
            grid-template-columns: minmax(220px, .75fr) minmax(0, 1.5fr) auto;
        }

        .admin-row-actions .button,
        .admin-row-actions button,
        .admin-list-head button {
            min-height: 38px;
            padding: 9px 12px;
            font-size: 14px;
            white-space: nowrap;
        }

        .button.secondary,
        button.secondary {
            background: #e7eef8;
            color: #12325f;
        }

        .button.danger-button,
        button.danger-button {
            background: #fee4e2;
            color: #b42318;
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

            .form-grid.form-wide {
                grid-template-columns: 1fr;
            }

            .admin-table-row {
                align-items: stretch;
            }

            .admin-row-actions {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 2px;
            }

            .admin-check-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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

            .admin-check-grid {
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

            @php
                $homepageMenuSections = collect();
                $homepageMenuLabels = [
                    'main-services' => 'Our Services',
                    'diagnostic-test-categories' => 'Accurate Tests, Reliable Results',
                    'specialist-doctors' => 'Specialist Doctors',
                    'why-choose-us' => 'Why Choose Us',
                    'facility-showcase' => 'Facility Showcase',
                ];

                if (auth()->user()?->hasPermission(\App\Support\AdminPermissions::MANAGE_CONTENT)) {
                    $homepageMenuSections = \App\Models\HomepageSection::query()
                        ->whereIn('section_key', array_keys($homepageMenuLabels))
                        ->get()
                        ->keyBy('section_key');
                }
            @endphp

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
                    <div class="nav-group">
                        <a href="{{ route('admin.homepage.index') }}" @class(['active' => request()->routeIs('admin.homepage.index')])>Homepage Builder</a>
                        <div class="nav-submenu" aria-label="Homepage section shortcuts">
                            @foreach ($homepageMenuLabels as $sectionKey => $sectionLabel)
                                @php
                                    $homepageMenuSection = $homepageMenuSections->get($sectionKey);
                                @endphp
                                @if ($homepageMenuSection)
                                    <a href="{{ route('admin.homepage.edit', $homepageMenuSection) }}" @class(['active' => request()->routeIs('admin.homepage.edit') && (int) request()->route('homepageSection')?->id === (int) $homepageMenuSection->id])>
                                        {{ $sectionLabel }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
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

            @if (isset($errors) && $errors->any())
                <div class="alert danger">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
