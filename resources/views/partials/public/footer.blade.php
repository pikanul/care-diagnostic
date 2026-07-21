@php
    $locale = app()->getLocale();
    $isBangla = $locale === 'bn';
    $siteName = $siteSettings?->{'hospital_name_'.$locale} ?? config('app.name');
    $siteNameLines = $siteName === 'Care Diagnostic & Physiotherapy Center'
        ? ['Care Diagnostic &', 'Physiotherapy Center']
        : preg_split('~\s*&\s*~', $siteName, 2);
    if (count($siteNameLines) === 2 && $siteName !== 'Care Diagnostic & Physiotherapy Center') {
        $siteNameLines[0] .= ' &';
    }
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
            <div class="footer-brand-head">
                @if ($siteSettings?->logo_path)
                    <img src="{{ asset('storage/'.$siteSettings->logo_path) }}" alt="{{ $siteName }}">
                @endif
                <h2 class="footer-brand-title">
                    @foreach ($siteNameLines as $line)
                        <span>{{ $line }}</span>
                    @endforeach
                </h2>
            </div>

        </div>

        <div class="footer-grid">
            @foreach ($footerSections as $section)
                @php
                    $sectionTitle = $isBangla ? $section->title_bn : $section->title_en;
                    $isNewsletterSection = strtolower((string) $section->title_en) === 'newsletter';
                @endphp
                @continue($isNewsletterSection)
                <section class="footer-section">
                    <h3>{{ $sectionTitle }}</h3>
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
        <span>© {{ date('Y') }} Care Diagnostic & Physiotherapy Center. All rights reserved.</span>
        <a class="developer-link" href="https://wa.me/8801711090660" target="_blank" rel="noopener">Developed by</a>
    </div>
</footer>
