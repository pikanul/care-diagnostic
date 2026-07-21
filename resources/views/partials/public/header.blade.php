@php
    $locale = app()->getLocale();
    $isBangla = $locale === 'bn';
    $titleKey = 'hospital_name_'.$locale;
    $siteName = $siteSettings?->{$titleKey} ?? config('app.name');
    $brandTagline = $isBangla ? 'আপনার সুস্থতা আমাদের অঙ্গীকার' : 'Trusted healthcare for every family';
    $openingHours = $siteSettings?->{'opening_hours_'.$locale} ?? null;
    $address = $siteSettings?->{'address_'.$locale} ?? null;
    $mapSource = trim((string) ($siteSettings?->google_map_embed ?? ''));
    $mapUrl = $address ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($address) : null;

    if ($mapSource !== '') {
        if (preg_match('~src=["\']([^"\']+)["\']~i', $mapSource, $mapMatch)) {
            $mapUrl = $mapMatch[1];
        } elseif (preg_match('~^https?://~i', $mapSource)) {
            $mapUrl = $mapSource;
        }
    }
    $localizedUrl = function (string $targetLocale): string {
        $path = request()->path();
        $localizedPath = preg_replace('~^(en|bn)(/|#|$)~', $targetLocale.'$2', $path);

        if ($localizedPath === $path) {
            $localizedPath = $targetLocale;
        }

        return url($localizedPath).(request()->getQueryString() ? '?'.request()->getQueryString() : '');
    };

    $menuUrl = function (string $url) use ($locale): string {
        if (preg_match('~^/(en|bn)(/|#|$)~', $url)) {
            return preg_replace('~^/(en|bn)(/|#|$)~', '/'.$locale.'$2', $url);
        }

        return $url;
    };

    $menuItems = $headerNavigationItems ?? collect();
@endphp

@if ($siteSettings?->header_top_bar_visible)
    <div class="topbar">
        <div class="container topbar-inner">
            <div class="topbar-meta">
                @if ($openingHours)
                    <span>{{ $openingHours }}</span>
                @endif
                @if ($siteSettings?->emergency_number)
                    <a href="tel:{{ $siteSettings->emergency_number }}">{{ $isBangla ? '২৪/৭ জরুরি হেল্পলাইন' : '24/7 Emergency Support' }}</a>
                @endif
                @if ($address)
                    <a class="topbar-map-link" href="{{ $mapUrl }}" target="_blank" rel="noopener">{{ $address }}</a>
                @endif
            </div>

            <div class="topbar-meta">
                <span class="language-switcher" aria-label="{{ $isBangla ? 'ভাষা পরিবর্তন' : 'Change language' }}">
                    <a href="{{ $localizedUrl('bn') }}" data-locale-switch class="{{ $isBangla ? 'is-active' : '' }}">বাংলা</a>
                    <span aria-hidden="true">|</span>
                    <a href="{{ $localizedUrl('en') }}" data-locale-switch class="{{ ! $isBangla ? 'is-active' : '' }}">English</a>
                </span>
            </div>
        </div>
    </div>
@endif

<header class="header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home', ['locale' => $locale]) }}">
            @if ($siteSettings?->logo_path)
                <picture>
                    @if ($siteSettings?->logo_mobile_path)
                        <source media="(max-width: 560px)" srcset="{{ asset('storage/'.$siteSettings->logo_mobile_path) }}">
                    @endif
                    <img src="{{ asset('storage/'.$siteSettings->logo_path) }}" alt="{{ $siteName }}">
                </picture>
            @else
                <div style="width:54px;height:54px;border-radius:8px;border:1px solid var(--line);display:grid;place-items:center;background:#fff;font-weight:700;">{{ mb_substr($siteName, 0, 1) }}</div>
            @endif
            <span class="brand-name">
                <strong>{{ $siteName }}</strong>
            </span>
        </a>

        <button class="menu-toggle" type="button" data-menu-toggle aria-expanded="false">Menu</button>

        <div class="nav-wrap" data-menu-panel>
            <nav class="nav-list" aria-label="Primary navigation">
                @foreach ($menuItems as $item)
                    <div class="nav-group">
                        <a href="{{ $menuUrl($item->url) }}" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>
                            {{ $isBangla ? $item->label_bn : $item->label_en }}
                            @if (! $isBangla && $item->label_en === 'Services')
                                <span aria-hidden="true">▾</span>
                            @endif
                        </a>

                        @if ($item->children->isNotEmpty())
                            <div class="nav-child">
                                @foreach ($item->children as $child)
                                    <a href="{{ $menuUrl($child->url) }}" @if($child->open_in_new_tab) target="_blank" rel="noopener" @endif>
                                        {{ $isBangla ? $child->label_bn : $child->label_en }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>

            <div class="header-actions">
                @if ($siteSettings?->contact_buttons_visible)
                    @if ($siteSettings?->book_appointment_button_url)
                        <a class="action-link primary" href="{{ $menuUrl($siteSettings->book_appointment_button_url) }}" data-phone-popup-trigger>
                            <span aria-hidden="true">▣</span>
                            {{ $isBangla ? ($siteSettings->book_appointment_button_label_bn ?: $siteSettings->book_appointment_button_label_en) : $siteSettings->book_appointment_button_label_en }}
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</header>
