@php
    $locale = app()->getLocale();
    $isBangla = $locale === 'bn';
    $footerText = $isBangla ? ($siteSettings?->footer_description_bn ?: $siteSettings?->footer_description_en) : ($siteSettings?->footer_description_en ?: $siteSettings?->footer_description_bn);
    $copyrightText = $isBangla ? ($siteSettings?->copyright_text_bn ?: $siteSettings?->copyright_text_en) : ($siteSettings?->copyright_text_en ?: $siteSettings?->copyright_text_bn);
    $socialLinks = $siteSettings?->social_links ?? [];

    $localizedLink = function (string $url) use ($locale): string {
        if (preg_match('~^/(en|bn)(/|#|$)~', $url)) {
            return preg_replace('~^/(en|bn)(/|#|$)~', '/'.$locale.'$2', $url);
        }

        return $url;
    };
@endphp

<footer class="footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <div style="display:flex;align-items:center;gap:12px;">
                @if ($siteSettings?->logo_path)
                    <img src="{{ asset('storage/'.$siteSettings->logo_path) }}" alt="{{ $siteSettings?->{'hospital_name_'.$locale} ?? config('app.name') }}">
                @endif
                <div>
                    <h2 style="margin:0 0 6px;font-size:22px;">{{ $siteSettings?->{'hospital_name_'.$locale} ?? config('app.name') }}</h2>
                    @if ($siteSettings?->address_en || $siteSettings?->address_bn)
                        <div class="footer-desc">{{ $isBangla ? ($siteSettings->address_bn ?: $siteSettings->address_en) : $siteSettings->address_en }}</div>
                    @endif
                </div>
            </div>

            @if ($footerText)
                <div class="footer-desc">{{ $footerText }}</div>
            @endif

            <div class="footer-links">
                @if ($siteSettings?->phone_primary)
                    <a href="tel:{{ $siteSettings->phone_primary }}">{{ $siteSettings->phone_primary }}</a>
                @endif
                @if ($siteSettings?->email)
                    <a href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email }}</a>
                @endif
                @if ($siteSettings?->whatsapp_link)
                    <a href="{{ $siteSettings->whatsapp_link }}" target="_blank" rel="noopener">WhatsApp</a>
                @endif
            </div>

            @if ($siteSettings?->newsletter_visible)
                <div class="newsletter">
                    <form class="newsletter-form" action="{{ $localizedLink('/en#appointment-cta') }}" method="get">
                        <input type="email" name="newsletter_email" placeholder="{{ $isBangla ? 'আপনার ইমেইল দিন' : 'Enter your email' }}" aria-label="{{ $isBangla ? 'নিউজলেটার ইমেইল' : 'Newsletter email' }}">
                        <button class="action-link primary" type="submit">{{ $isBangla ? 'পাঠান' : 'Send' }}</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="footer-grid">
            @foreach ($footerSections as $section)
                <section class="footer-section">
                    <h3>{{ $isBangla ? $section->title_bn : $section->title_en }}</h3>
                    <div class="footer-links">
                        @foreach ($section->links as $link)
                            <a href="{{ $localizedLink($link->url) }}" @if($link->open_in_new_tab) target="_blank" rel="noopener" @endif>
                                {{ $isBangla ? $link->label_bn : $link->label_en }}
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>

    <div class="container footer-bottom">
            <span>{{ $copyrightText ?: ($siteSettings?->{'hospital_name_'.$locale} ?? config('app.name')).' © '.date('Y') }}</span>
        <div class="footer-social">
            @foreach (($socialLinks ?? []) as $platform => $url)
                @if ($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener">{{ ucfirst($platform) }}</a>
                @endif
            @endforeach
        </div>
    </div>
</footer>
