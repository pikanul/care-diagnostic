<!DOCTYPE html>
@php
    $currentLocale = app()->getLocale();
    $siteSettings = $siteSettings ?? null;
@endphp
<html lang="{{ str_replace('_', '-', $currentLocale) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f7fb">
    <title>{{ $siteSettings?->{'hospital_name_'.$currentLocale} ?? config('app.name') }}</title>
    @if ($siteSettings?->favicon_path)
        <link rel="icon" href="{{ asset('storage/'.$siteSettings->favicon_path) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --panel: #ffffff;
            --text: #162033;
            --muted: #667085;
            --line: #dbe2ee;
            --accent: #0f766e;
            --accent-dark: #0b5d56;
            --danger: #b42318;
        }

        * { box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, Arial, Helvetica, sans-serif;
            overflow-x: hidden;
            min-width: 320px;
        }

        html[lang="bn"] body {
            font-family: 'Hind Siliguri', 'Noto Sans Bengali', Arial, Helvetica, sans-serif;
        }

        a { color: inherit; }

        img,
        video,
        iframe {
            max-width: 100%;
        }

        img,
        video {
            height: auto;
        }

        button,
        input,
        select,
        textarea {
            max-width: 100%;
            font: inherit;
        }

        input,
        select,
        textarea {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .container {
            width: min(1840px, calc(100% - 180px));
            margin: 0 auto;
            min-width: 0;
        }

        .topbar {
            background: #062a61;
            color: #ffffff;
            font-size: 17px;
            font-weight: 700;
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            gap: 28px;
            align-items: center;
            min-height: 56px;
            padding: 0;
            min-width: 0;
        }

        .topbar-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 18px 38px;
            align-items: center;
            min-width: 0;
        }

        .topbar-meta:first-child span::before,
        .topbar-meta:first-child a::before {
            display: inline-grid;
            place-items: center;
            width: 24px;
            height: 24px;
            margin-right: 10px;
            border: 2px solid rgba(255,255,255,.9);
            border-radius: 999px;
            font-size: 12px;
            line-height: 1;
        }

        .topbar-meta:first-child span:first-child::before {
            content: "◷";
        }

        .topbar-meta:first-child a::before {
            content: "▣";
        }

        .topbar-meta:first-child span:last-child::before {
            content: "●";
            border: 0;
            font-size: 22px;
            color: #fff;
        }

        .topbar a {
            color: #ffffff;
            text-decoration: none;
        }

        .topbar-social {
            width: 34px;
            height: 34px;
            display: inline-grid;
            place-items: center;
            border-radius: 999px;
            background: #083878;
            color: #fff;
            font-size: 17px;
            font-weight: 800;
            line-height: 1;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 30;
            background: #ffffff;
            backdrop-filter: none;
            border-top: 4px solid #062a61;
            border-bottom: 1px solid var(--line);
        }

        .header-inner {
            display: grid;
            grid-template-columns: minmax(350px, auto) minmax(0, 1fr);
            gap: 28px;
            align-items: center;
            min-height: 92px;
            padding: 0;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            min-width: 0;
        }

        .brand picture {
            display: block;
            flex: 0 0 auto;
        }

        .brand img {
            display: block;
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 999px;
            background: #fff;
            border: 2px solid #1a66e8;
            padding: 4px;
        }

        .brand-name {
            display: grid;
            gap: 2px;
            min-width: 0;
            max-width: 300px;
        }

        .brand-name strong {
            font-size: 21px;
            line-height: 1.22;
            color: #061447;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .brand-name span {
            color: var(--muted);
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .menu-toggle {
            display: none;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
            border-radius: 8px;
            padding: 10px 12px;
            font: inherit;
            font-weight: 600;
            min-height: 44px;
        }

        .nav-wrap {
            display: flex;
            justify-content: flex-end;
            gap: 30px;
            align-items: center;
        }

        .nav-list {
            display: flex;
            gap: 32px;
            align-items: center;
            flex-wrap: nowrap;
        }

        .nav-list a {
            text-decoration: none;
            color: #071436;
            font-weight: 800;
            font-size: 18px;
            padding: 48px 0;
            line-height: 1;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            position: relative;
        }

        .nav-list a:hover {
            color: var(--accent);
        }

        .nav-list .nav-group:first-child > a {
            color: #075bd8;
        }

        .nav-list .nav-group:first-child > a::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 19px;
            height: 4px;
            background: #12964f;
            border-radius: 999px;
        }

        .nav-group {
            position: relative;
        }

        .nav-group > .nav-child {
            position: absolute;
            left: 0;
            top: 100%;
            display: none;
            min-width: 200px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 8px;
            box-shadow: 0 12px 24px rgba(16, 24, 40, 0.08);
        }

        .nav-group:hover > .nav-child {
            display: grid;
        }

        .nav-child a {
            padding: 8px 10px;
            border-radius: 6px;
        }

        .nav-child a:hover {
            background: #eef6f5;
        }

        .header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .action-btn,
        .action-link {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 10px 14px;
            text-decoration: none;
            font-weight: 700;
            background: #fff;
            color: var(--text);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            text-align: center;
            justify-content: center;
            overflow-wrap: anywhere;
        }

        .action-btn.primary,
        .action-link.primary {
            background: #0a8f3e;
            color: #fff;
            border-color: #0a8f3e;
            box-shadow: 0 7px 16px rgba(10,143,62,.22);
        }

        .header-actions .action-link.primary {
            min-height: 66px;
            padding: 0 28px;
            border-radius: 10px;
            font-size: 18px;
            white-space: nowrap;
        }

        .action-btn.danger,
        .action-link.danger {
            background: var(--danger);
            color: #fff;
            border-color: var(--danger);
        }

        .content {
            min-height: 36vh;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 18px;
            min-width: 0;
        }

        .footer {
            margin-top: 48px;
            background: #10233d;
            color: #dfe7f7;
        }

        .footer-inner {
            padding: 36px 0 18px;
            display: grid;
            grid-template-columns: 1.4fr 2fr;
            gap: 28px;
        }

        .footer-brand {
            display: grid;
            gap: 12px;
        }

        .footer-brand img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 8px;
            background: #fff;
            padding: 4px;
        }

        .footer-desc {
            color: #c8d5ee;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .footer-section h3 {
            margin: 0 0 12px;
            font-size: 16px;
        }

        .footer-links {
            display: grid;
            gap: 8px;
        }

        .footer-links a {
            color: #dfe7f7;
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        .footer-links a:hover {
            color: #fff;
        }

        .newsletter {
            margin-top: 18px;
            padding: 14px;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.12);
            padding: 14px 0 18px;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            color: #b9c7df;
        }

        .footer-social {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-social a {
            color: #dfe7f7;
            text-decoration: none;
        }

        .page-shell {
            padding: 24px 0 0;
        }

        .mobile-action-bar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 60;
            display: none;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1px;
            background: rgba(219, 226, 238, 0.95);
            border-top: 1px solid var(--line);
            padding: 0 0 env(safe-area-inset-bottom);
        }

        .mobile-action-bar a {
            min-width: 0;
            min-height: 54px;
            display: grid;
            place-items: center;
            padding: 8px 6px;
            background: #fff;
            color: var(--text);
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.2;
            text-align: center;
            overflow-wrap: anywhere;
        }

        .mobile-action-bar a.primary {
            background: var(--accent);
            color: #fff;
        }

        .mobile-action-bar a.danger {
            background: var(--danger);
            color: #fff;
        }

        @media (max-width: 1500px) {
            .container {
                width: min(1320px, calc(100% - 64px));
            }

            .header-inner {
                grid-template-columns: minmax(320px, auto) minmax(0, 1fr);
                gap: 20px;
            }

            .brand img {
                width: 66px;
                height: 66px;
            }

            .brand-name strong {
                font-size: 20px;
            }

            .nav-list {
                gap: 16px;
            }

            .nav-wrap {
                gap: 18px;
            }

            .nav-list a {
                font-size: 14px;
            }

            .header-actions .action-link.primary {
                min-height: 54px;
                padding: 0 18px;
                font-size: 15px;
            }
        }

        @media (max-width: 1180px) {
            .header-inner {
                grid-template-columns: auto auto;
                gap: 12px;
                min-height: auto;
                padding: 12px 0;
            }

            .nav-wrap {
                grid-column: 1 / -1;
                display: none;
                justify-content: flex-start;
                flex-direction: column;
                align-items: stretch;
                max-width: 100%;
                overflow: hidden;
            }

            .nav-wrap.is-open {
                display: flex;
            }

            .nav-list {
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
            }

            .nav-list a {
                padding: 12px 0;
                min-height: 44px;
                display: flex;
                align-items: center;
            }

            .nav-list .nav-group:first-child > a::after {
                bottom: 4px;
            }

            .menu-toggle {
                display: inline-flex;
                justify-self: end;
            }

            .header-actions {
                justify-content: flex-start;
                flex-direction: column;
            }

            .header-actions .action-link {
                width: 100%;
            }

            .footer-inner,
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .content {
                padding-bottom: 66px;
            }

            .mobile-action-bar {
                display: grid;
            }
        }

        @media (max-width: 560px) {
            .container {
                width: min(100% - 20px, 1180px);
            }

            .topbar-inner {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }

            .header-inner {
                padding: 10px 0;
            }

            .brand {
                gap: 8px;
            }

            .brand img {
                width: 54px;
                height: 54px;
            }

            .brand-name strong {
                font-size: 16px;
            }

            .brand-name span {
                font-size: 12px;
            }
        }

        @media (max-width: 360px) {
            .container {
                width: min(100% - 16px, 1180px);
            }

            .brand-name strong {
                font-size: 14px;
            }

            .menu-toggle {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    @include('partials.public.header')

    <main class="content">
        @yield('content')
    </main>

    @include('partials.public.footer')

    @php
        $localizePublicUrl = function (string $url) use ($currentLocale): string {
            if (preg_match('~^/(en|bn)(/|#|$)~', $url)) {
                return preg_replace('~^/(en|bn)(/|#|$)~', '/'.$currentLocale.'$2', $url);
            }

            return $url;
        };
        $mobileCallUrl = $siteSettings?->emergency_button_url ?: ($siteSettings?->emergency_number ? 'tel:'.$siteSettings->emergency_number : ($siteSettings?->phone_primary ? 'tel:'.$siteSettings->phone_primary : '#'));
        $mobileAppointmentUrl = $localizePublicUrl($siteSettings?->book_appointment_button_url ?: '/'.$currentLocale.'#appointment-cta');
        $mobileSampleUrl = '/'.$currentLocale.'#home-sample-collection';
    @endphp

    <nav class="mobile-action-bar" aria-label="{{ $currentLocale === 'bn' ? 'দ্রুত মোবাইল অ্যাকশন' : 'Mobile quick actions' }}">
        <a class="danger" href="{{ $mobileCallUrl }}">{{ $currentLocale === 'bn' ? 'কল' : 'Call' }}</a>
        <a class="primary" href="{{ $mobileAppointmentUrl }}">{{ $currentLocale === 'bn' ? 'অ্যাপয়েন্টমেন্ট' : 'Appointment' }}</a>
        <a href="{{ $mobileSampleUrl }}">{{ $currentLocale === 'bn' ? 'হোম স্যাম্পল' : 'Home sample' }}</a>
    </nav>

    <script>
        const toggle = document.querySelector('[data-menu-toggle]');
        const menu = document.querySelector('[data-menu-panel]');

        if (toggle && menu) {
            toggle.addEventListener('click', () => {
                const isOpen = menu.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }
    </script>
</body>
</html>
