<!DOCTYPE html>
@php
    $currentLocale = app()->getLocale();
    $siteSettings = $siteSettings ?? null;
    $seoSettings = array_merge(\App\Models\GlobalSetting::defaults()['seo_settings'] ?? [], $siteSettings?->seo_settings ?? []);
    $marketingTools = collect(data_get($siteSettings?->marketing_tools, 'tools', []))->where('is_active', true);
    $toolByProvider = $marketingTools->keyBy(fn ($tool) => data_get($tool, 'provider'));
    $visitorSettings = array_merge(\App\Models\GlobalSetting::defaults()['visitor_tracking_settings'] ?? [], $siteSettings?->visitor_tracking_settings ?? []);
    $cookieConsentEnabled = (bool) data_get($visitorSettings, 'cookie_consent_enabled', true);
    $webVitalsEnabled = (bool) data_get($visitorSettings, 'web_vitals_enabled', true);
    $scriptType = $cookieConsentEnabled ? 'text/plain' : 'text/javascript';
    $scriptConsentAttrs = $cookieConsentEnabled ? 'data-cookie-script="analytics"' : '';
    $ga4Id = data_get($toolByProvider->get('google_analytics_4'), 'tracking_id');
    $gtmId = data_get($toolByProvider->get('google_tag_manager'), 'tracking_id');
    $metaPixelId = data_get($toolByProvider->get('meta_pixel'), 'tracking_id');
    $clarityId = data_get($toolByProvider->get('microsoft_clarity'), 'tracking_id');
    $localizedSeo = function (string $key, ?string $fallback = null) use ($seoSettings, $currentLocale): ?string {
        return data_get($seoSettings, $key.'_'.$currentLocale)
            ?: data_get($seoSettings, $key.'_en')
            ?: $fallback;
    };
    $metaTitle = $localizedSeo('meta_title', $siteSettings?->{'hospital_name_'.$currentLocale} ?? config('app.name'));
    $metaDescription = $localizedSeo('meta_description');
    $metaKeywords = $localizedSeo('meta_keywords');
    $canonicalUrl = data_get($seoSettings, 'canonical_url') ?: url()->current();
    $ogImagePath = data_get($seoSettings, 'og_image_path');
    $ogImageUrl = $ogImagePath ? asset('storage/'.$ogImagePath) : null;
    $alternatePath = preg_replace('~^/(en|bn)(/|$)~', '/', request()->getPathInfo()) ?: '/';
    $schemaAddress = $siteSettings?->{'address_'.$currentLocale} ?: $siteSettings?->address_en;
    $medicalClinicSchema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'MedicalClinic',
        'name' => $siteSettings?->{'hospital_name_'.$currentLocale} ?? config('app.name'),
        'url' => url('/'.$currentLocale),
        'logo' => $siteSettings?->logo_path ? asset('storage/'.$siteSettings->logo_path) : null,
        'image' => $ogImageUrl,
        'telephone' => $siteSettings?->phone_primary,
        'email' => $siteSettings?->email,
        'address' => $schemaAddress,
        'medicalSpecialty' => ['Diagnostic', 'Physiotherapy', 'PrimaryCare'],
        'sameAs' => collect($siteSettings?->social_links ?? [])->filter()->values()->all(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
<html lang="{{ str_replace('_', '-', $currentLocale) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#004aa5">
    <title>{{ $metaTitle }}</title>
    @if ($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if ($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <meta name="robots" content="{{ data_get($seoSettings, 'robots', 'index,follow') }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="en" href="{{ url('/en'.($alternatePath === '/' ? '' : $alternatePath)) }}">
    <link rel="alternate" hreflang="bn" href="{{ url('/bn'.($alternatePath === '/' ? '' : $alternatePath)) }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/en'.($alternatePath === '/' ? '' : $alternatePath)) }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    @if ($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if ($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}">
    @endif
    <meta name="twitter:card" content="{{ $ogImageUrl ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    @if ($metaDescription)
        <meta name="twitter:description" content="{{ $metaDescription }}">
    @endif
    @if ($ogImageUrl)
        <meta name="twitter:image" content="{{ $ogImageUrl }}">
    @endif
    @if (data_get($seoSettings, 'google_site_verification'))
        <meta name="google-site-verification" content="{{ data_get($seoSettings, 'google_site_verification') }}">
    @endif
    @if (data_get($seoSettings, 'bing_site_verification'))
        <meta name="msvalidate.01" content="{{ data_get($seoSettings, 'bing_site_verification') }}">
    @endif
    @if (data_get($seoSettings, 'facebook_domain_verification'))
        <meta name="facebook-domain-verification" content="{{ data_get($seoSettings, 'facebook_domain_verification') }}">
    @endif
    @if ($siteSettings?->favicon_path)
        <link rel="icon" href="{{ asset('storage/'.$siteSettings->favicon_path) }}">
    @endif
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    <link rel="dns-prefetch" href="//connect.facebook.net">
    <link rel="dns-prefetch" href="//www.clarity.ms">
    @if ($ga4Id)
        <script type="{{ $scriptType }}" {!! $scriptConsentAttrs !!} async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
        <script type="{{ $scriptType }}" {!! $scriptConsentAttrs !!}>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $ga4Id }}', { anonymize_ip: true });
        </script>
    @endif
    @if ($gtmId)
        <script type="{{ $scriptType }}" {!! $scriptConsentAttrs !!}>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ $gtmId }}');
        </script>
    @endif
    @if ($metaPixelId)
        <script type="{{ $scriptType }}" {!! $scriptConsentAttrs !!}>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
            (window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $metaPixelId }}');
            fbq('track', 'PageView');
        </script>
    @endif
    @if ($clarityId)
        <script type="{{ $scriptType }}" {!! $scriptConsentAttrs !!}>
            (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, 'clarity', 'script', '{{ $clarityId }}');
        </script>
    @endif
    @foreach ($marketingTools as $tool)
        @if (data_get($tool, 'script_head'))
            {!! data_get($tool, 'script_head') !!}
        @endif
    @endforeach
    <script type="application/ld+json">
        {!! $medicalClinicSchema !!}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4f9ff;
            --panel: #ffffff;
            --text: #082352;
            --muted: #5c769b;
            --line: #cfe3f7;
            --accent: #0b66c3;
            --accent-dark: #003f95;
            --accent-cyan: #0bb7c7;
            --danger: #b42318;
            --topbar-height: 42px;
            --header-height: 96px;
        }

        * { box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: calc(var(--header-height) + 16px);
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
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 41;
            background: linear-gradient(135deg, #b7d8f0 0%, #8bbde0 52%, #d8edf9 100%);
            color: #073363;
            font-size: 14px;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,.58);
            transition: transform .24s ease, opacity .24s ease;
            will-change: transform;
        }

        body.topbar-hidden .topbar {
            opacity: 0;
            transform: translateY(calc(-1 * var(--topbar-height)));
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            min-height: 42px;
            padding: 0;
            min-width: 0;
        }

        .topbar-meta {
            display: flex;
            flex-wrap: nowrap;
            gap: 18px;
            align-items: center;
            min-width: 0;
        }

        .topbar-meta:first-child {
            flex: 1 1 auto;
        }

        .topbar-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }

        .topbar-icon {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            color: #064b8e;
            filter: drop-shadow(0 2px 4px rgba(255,255,255,.52));
        }

        .topbar-meta:first-child span:last-child,
        .topbar-meta:first-child .topbar-map-link {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .topbar-meta:last-child {
            flex: 0 0 auto;
        }

        .topbar a {
            color: #073363;
            text-decoration: none;
        }

        .topbar-social {
            width: 34px;
            height: 34px;
            display: inline-grid;
            place-items: center;
            border-radius: 999px;
            background: #075db8;
            color: #fff;
            font-size: 17px;
            font-weight: 800;
            line-height: 1;
        }

        .language-switcher {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: rgba(7,51,99,.78);
            font-size: 15px;
            font-weight: 700;
            white-space: nowrap;
        }

        .language-switcher a {
            color: rgba(7,51,99,.78);
            text-decoration: none;
        }

        .language-switcher a:hover,
        .language-switcher a.is-active {
            color: #004aa5;
        }

        .language-switcher a.is-active {
            font-weight: 900;
        }

        .header {
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            right: 0;
            z-index: 40;
            background: linear-gradient(135deg, rgba(230,244,252,.98) 0%, rgba(184,218,240,.96) 46%, rgba(246,251,255,.98) 100%);
            backdrop-filter: blur(12px) saturate(1.08);
            -webkit-backdrop-filter: blur(12px) saturate(1.08);
            border-top: 4px solid #7fb6dc;
            border-bottom: 1px solid rgba(126,177,213,.5);
            box-shadow: 0 12px 26px rgba(35, 96, 144, .12);
            overflow: hidden;
            transition: top .24s ease;
        }

        body.topbar-hidden .header {
            top: 0;
        }

        .header::after {
            content: "";
            position: absolute;
            top: -42px;
            right: -72px;
            width: 360px;
            height: 170px;
            pointer-events: none;
            background: radial-gradient(circle at 55% 35%, rgba(255,255,255,.98) 0 26%, rgba(236,248,255,.78) 27% 48%, rgba(255,255,255,0) 72%);
            filter: blur(8px);
            z-index: 0;
        }

        .header-inner {
            position: relative;
            z-index: 1;
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
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            width: 76px;
            height: 76px;
            border-radius: 18px;
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(11,102,195,.18);
            box-shadow: 0 12px 24px rgba(0,74,165,.12), inset 0 0 0 5px rgba(255,255,255,.7);
        }

        .brand img {
            display: block;
            width: 66px;
            height: 66px;
            object-fit: contain;
            border-radius: 12px;
            background: transparent;
            border: 0;
            padding: 0;
            image-rendering: auto;
            filter: drop-shadow(0 3px 7px rgba(0,74,165,.12));
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
            background: linear-gradient(135deg, #004aa5, #0bb7c7);
            color: #fff;
            border-color: #0b66c3;
            box-shadow: 0 7px 16px rgba(0,74,165,.22);
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
            padding-top: calc(var(--topbar-height) + var(--header-height));
            min-height: 36vh;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 18px;
            min-width: 0;
        }

        .phone-modal {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(5, 23, 54, .48);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .phone-modal.is-open {
            display: flex;
        }

        .phone-modal-card {
            width: min(100%, 430px);
            border: 1px solid rgba(255,255,255,.62);
            border-radius: 18px;
            padding: 20px;
            background: rgba(255,255,255,.88);
            box-shadow: 0 24px 70px rgba(5, 35, 82, .24);
            color: var(--text);
        }

        .phone-modal-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .phone-modal-head h2 {
            margin: 0;
            font-size: 22px;
            line-height: 1.2;
        }

        .phone-modal-close {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            padding: 0;
            background: #e7eef8;
            color: #0b2b60;
            font-size: 22px;
            line-height: 1;
        }

        .phone-modal-list {
            display: grid;
            gap: 10px;
        }

        .phone-modal-list a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 54px;
            border: 1px solid rgba(11,102,195,.18);
            border-radius: 13px;
            padding: 0 16px;
            background: linear-gradient(135deg, #ffffff 0%, #eef8ff 100%);
            color: #08306f;
            text-decoration: none;
            font-size: 18px;
            font-weight: 900;
            box-shadow: 0 10px 24px rgba(11,102,195,.08);
        }

        .phone-modal-list a::after {
            content: "Call";
            border-radius: 999px;
            padding: 6px 10px;
            background: #079455;
            color: #fff;
            font-size: 12px;
            font-weight: 900;
        }

        html[lang="bn"] .phone-modal-list a::after {
            content: "কল";
        }

        .footer {
            margin-top: 48px;
            background:
                linear-gradient(135deg, rgba(8,74,138,.9) 0%, rgba(76,142,190,.86) 48%, rgba(211,236,249,.94) 100%),
                linear-gradient(180deg, #c7e3f4 0%, #8bbde0 100%);
            color: #f4fbff;
        }

        .footer-inner {
            padding: 36px 0 18px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
            align-items: start;
        }

        .footer-brand {
            display: grid;
            gap: 12px;
        }

        .footer-brand img {
            width: 92px;
            height: 92px;
            object-fit: contain;
            border-radius: 8px;
            background: #fff;
            padding: 4px;
        }

        .footer-brand-head {
            display: grid;
            gap: 12px;
        }

        .footer-brand-title {
            margin: 0;
            font-size: 22px;
            line-height: 1.18;
            font-weight: 900;
        }

        .footer-brand-title span {
            display: block;
        }

        .footer-desc {
            color: #eff8ff;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        .footer-address-link {
            color: #ffffff;
            line-height: 1.6;
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        .footer-address-link:hover {
            color: #dff3ff;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .footer-grid {
            display: contents;
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
            color: #f4fbff;
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

        .newsletter-form {
            display: flex;
            gap: 8px;
            align-items: center;
            max-width: 560px;
        }

        .newsletter-form input {
            min-width: 0;
            height: 44px;
            padding: 0 12px;
            border-radius: 7px;
            border: 1px solid rgba(255,255,255,.28);
            font-size: 14px;
        }

        .newsletter-form .action-link {
            flex: 0 0 auto;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1;
            white-space: nowrap;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.18);
            background: rgba(2, 33, 73, .24);
            padding: 14px 0 18px;
            display: grid;
            place-items: center;
            gap: 8px;
            text-align: center;
            color: #e7f4ff;
        }

        .footer-bottom > span {
            max-width: 100%;
            white-space: nowrap;
            font-size: clamp(10px, 2vw, 14px);
        }

        .developer-link {
            color: #fff;
            font-weight: 900;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
        }

        .developer-link:hover {
            background: #078b4f;
        }

        .footer-social {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-social a {
            color: #06366f;
            background: #eef8ff;
            border: 1px solid rgba(255,255,255,.68);
            border-radius: 999px;
            padding: 6px 11px;
            font-weight: 800;
            text-decoration: none;
        }

        .footer-social a:hover {
            color: #ffffff;
            background: #078b4f;
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

        .cookie-consent {
            position: fixed;
            left: 18px;
            right: 18px;
            bottom: 18px;
            z-index: 120;
            display: none;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            width: min(960px, calc(100% - 36px));
            margin: 0 auto;
            border: 1px solid rgba(255,255,255,.72);
            border-radius: 14px;
            padding: 14px;
            background: rgba(255,255,255,.92);
            color: var(--text);
            box-shadow: 0 22px 70px rgba(5, 35, 82, .22);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .cookie-consent.is-visible {
            display: grid;
        }

        .cookie-consent p {
            margin: 0;
            color: var(--muted);
            line-height: 1.45;
        }

        .cookie-consent-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
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
                width: 62px;
                height: 62px;
            }

            .brand picture {
                width: 72px;
                height: 72px;
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
            :root {
                --header-height: 82px;
            }

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

            .cookie-consent {
                bottom: 66px;
                grid-template-columns: 1fr;
            }

            .cookie-consent-actions {
                justify-content: stretch;
            }

            .cookie-consent-actions button,
            .cookie-consent-actions a {
                flex: 1 1 auto;
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
                width: 50px;
                height: 50px;
            }

            .brand picture {
                width: 58px;
                height: 58px;
                border-radius: 14px;
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
    @foreach ($marketingTools as $tool)
        @if (data_get($tool, 'script_body'))
            {!! data_get($tool, 'script_body') !!}
        @endif
    @endforeach

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
        <a class="danger" href="{{ $mobileCallUrl }}" data-phone-popup-trigger>{{ $currentLocale === 'bn' ? 'কল' : 'Call' }}</a>
        <a class="primary" href="{{ $mobileAppointmentUrl }}" data-phone-popup-trigger>{{ $currentLocale === 'bn' ? 'অ্যাপয়েন্টমেন্ট' : 'Appointment' }}</a>
        <a href="{{ $mobileSampleUrl }}">{{ $currentLocale === 'bn' ? 'হোম স্যাম্পল' : 'Home sample' }}</a>
    </nav>

    <div class="phone-modal" data-phone-modal aria-hidden="true">
        <div class="phone-modal-card" role="dialog" aria-modal="true" aria-labelledby="phone-modal-title">
            <div class="phone-modal-head">
                <div>
                    <h2 id="phone-modal-title">{{ $currentLocale === 'bn' ? 'কল করুন' : 'Call Us Now' }}</h2>
                    <p class="muted-text" style="margin:6px 0 0;">{{ $currentLocale === 'bn' ? 'নম্বর নির্বাচন করুন' : 'Choose a number to call directly' }}</p>
                </div>
                <button class="phone-modal-close" type="button" data-phone-modal-close aria-label="{{ $currentLocale === 'bn' ? 'বন্ধ করুন' : 'Close' }}">×</button>
            </div>
            <div class="phone-modal-list">
                <a href="tel:01734762211">01734762211</a>
                <a href="tel:01958404940">01958404940</a>
                <a href="tel:01730961359">01730961359</a>
            </div>
        </div>
    </div>

    @if ($cookieConsentEnabled)
        <div class="cookie-consent" data-cookie-consent role="dialog" aria-live="polite" aria-label="{{ $currentLocale === 'bn' ? 'কুকি সম্মতি' : 'Cookie consent' }}">
            <p>{{ data_get($visitorSettings, 'cookie_banner_text_'.$currentLocale) ?: data_get($visitorSettings, 'cookie_banner_text_en') }}</p>
            <div class="cookie-consent-actions">
                @if (data_get($visitorSettings, 'cookie_privacy_url'))
                    <a class="action-link" href="{{ data_get($visitorSettings, 'cookie_privacy_url') }}">{{ $currentLocale === 'bn' ? 'গোপনীয়তা' : 'Privacy' }}</a>
                @endif
                <button class="action-link" type="button" data-cookie-reject>{{ $currentLocale === 'bn' ? 'প্রয়োজনীয় মাত্র' : 'Essential only' }}</button>
                <button class="action-link primary" type="button" data-cookie-accept>{{ $currentLocale === 'bn' ? 'সম্মতি দিন' : 'Accept' }}</button>
            </div>
        </div>
    @endif

    <script>
        const toggle = document.querySelector('[data-menu-toggle]');
        const menu = document.querySelector('[data-menu-panel]');
        const phoneModal = document.querySelector('[data-phone-modal]');
        const phoneModalClose = document.querySelector('[data-phone-modal-close]');
        const topbar = document.querySelector('.topbar');
        const cookieConsent = document.querySelector('[data-cookie-consent]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const activateCookieScripts = () => {
            document.querySelectorAll('script[type="text/plain"][data-cookie-script="analytics"]').forEach((script) => {
                const activeScript = document.createElement('script');
                Array.from(script.attributes).forEach((attribute) => {
                    if (!['type', 'data-cookie-script'].includes(attribute.name)) {
                        activeScript.setAttribute(attribute.name, attribute.value);
                    }
                });
                activeScript.type = 'text/javascript';
                activeScript.text = script.textContent;
                script.replaceWith(activeScript);
            });
        };

        const cookieChoice = localStorage.getItem('care_cookie_consent');
        if (cookieChoice === 'accepted') {
            activateCookieScripts();
        } else if (cookieConsent && !cookieChoice) {
            cookieConsent.classList.add('is-visible');
        }

        document.querySelector('[data-cookie-accept]')?.addEventListener('click', () => {
            localStorage.setItem('care_cookie_consent', 'accepted');
            cookieConsent?.classList.remove('is-visible');
            activateCookieScripts();
        });

        document.querySelector('[data-cookie-reject]')?.addEventListener('click', () => {
            localStorage.setItem('care_cookie_consent', 'essential');
            cookieConsent?.classList.remove('is-visible');
        });

        const updateTopbarState = () => {
            if (! topbar) {
                document.documentElement.style.setProperty('--topbar-height', '0px');
                return;
            }

            document.documentElement.style.setProperty('--topbar-height', `${topbar.offsetHeight}px`);
            document.body.classList.toggle('topbar-hidden', window.scrollY > 8);
        };

        updateTopbarState();
        window.addEventListener('scroll', updateTopbarState, { passive: true });
        window.addEventListener('resize', updateTopbarState);

        const openPhoneModal = () => {
            if (! phoneModal) {
                return;
            }

            phoneModal.classList.add('is-open');
            phoneModal.setAttribute('aria-hidden', 'false');
            phoneModalClose?.focus();
        };

        const closePhoneModal = () => {
            if (! phoneModal) {
                return;
            }

            phoneModal.classList.remove('is-open');
            phoneModal.setAttribute('aria-hidden', 'true');
        };

        document.querySelectorAll('[data-phone-popup-trigger]').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                openPhoneModal();
            });
        });

        phoneModalClose?.addEventListener('click', closePhoneModal);
        phoneModal?.addEventListener('click', (event) => {
            if (event.target === phoneModal) {
                closePhoneModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closePhoneModal();
            }
        });

        if (toggle && menu) {
            toggle.addEventListener('click', () => {
                const isOpen = menu.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        document.querySelectorAll('[data-locale-switch]').forEach((link) => {
            link.addEventListener('click', (event) => {
                if (!window.location.hash) {
                    return;
                }

                event.preventDefault();
                window.location.href = link.href + window.location.hash;
            });
        });

        const trackMarketingEvent = (name, parameters = {}) => {
            if (typeof window.gtag === 'function') {
                window.gtag('event', name, parameters);
            }
            if (typeof window.fbq === 'function') {
                window.fbq('trackCustom', name, parameters);
            }
        };

        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (!link) {
                return;
            }

            const href = link.getAttribute('href') || '';
            if (href.startsWith('tel:')) {
                trackMarketingEvent('phone_click', { link_url: href });
            } else if (href.toLowerCase().includes('whatsapp')) {
                trackMarketingEvent('whatsapp_click', { link_url: href });
            } else if (href.includes('appointment')) {
                trackMarketingEvent('appointment_click', { link_url: href });
            }
        });

        @if ($webVitalsEnabled)
            (() => {
                const postMetric = (metric, value, rating, metadata = {}) => {
                    if (!csrfToken) {
                        return;
                    }

                    fetch('{{ route('web-vitals.store') }}', {
                        method: 'POST',
                        keepalive: true,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            metric,
                            value,
                            rating,
                            path: window.location.pathname,
                            url: window.location.href,
                            metadata,
                        }),
                    }).catch(() => {});
                };

                const ratingFor = (metric, value) => {
                    const thresholds = {
                        LCP: [2500, 4000],
                        CLS: [0.1, 0.25],
                        FID: [100, 300],
                        INP: [200, 500],
                        TTFB: [800, 1800],
                    }[metric] || [0, 0];
                    return value <= thresholds[0] ? 'good' : (value <= thresholds[1] ? 'needs-improvement' : 'poor');
                };

                window.addEventListener('load', () => {
                    const nav = performance.getEntriesByType('navigation')[0];
                    if (nav) {
                        const ttfb = nav.responseStart - nav.requestStart;
                        postMetric('TTFB', Math.max(0, ttfb), ratingFor('TTFB', ttfb));
                    }
                });

                try {
                    new PerformanceObserver((list) => {
                        const entry = list.getEntries().at(-1);
                        if (entry) {
                            postMetric('LCP', entry.startTime, ratingFor('LCP', entry.startTime), { element: entry.element?.tagName || null });
                        }
                    }).observe({ type: 'largest-contentful-paint', buffered: true });
                } catch (error) {}

                try {
                    let clsValue = 0;
                    new PerformanceObserver((list) => {
                        list.getEntries().forEach((entry) => {
                            if (!entry.hadRecentInput) {
                                clsValue += entry.value;
                            }
                        });
                        postMetric('CLS', clsValue, ratingFor('CLS', clsValue));
                    }).observe({ type: 'layout-shift', buffered: true });
                } catch (error) {}

                try {
                    new PerformanceObserver((list) => {
                        list.getEntries().forEach((entry) => {
                            const value = entry.processingStart - entry.startTime;
                            postMetric('FID', value, ratingFor('FID', value), { name: entry.name });
                        });
                    }).observe({ type: 'first-input', buffered: true });
                } catch (error) {}
            })();
        @endif
    </script>
</body>
</html>
