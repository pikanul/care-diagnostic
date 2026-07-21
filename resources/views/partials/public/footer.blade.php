@php
    $locale = app()->getLocale();
    $isBangla = $locale === 'bn';
    $copyrightText = $isBangla ? ($siteSettings?->copyright_text_bn ?: $siteSettings?->copyright_text_en) : ($siteSettings?->copyright_text_en ?: $siteSettings?->copyright_text_bn);
    $localizedLink = function (string $url) use ($locale): string {
        if (preg_match('~^/(en|bn)(/|#|$)~', $url)) {
            return preg_replace('~^/(en|bn)(/|#|$)~', '/'.$locale.'$2', $url);
        }

        return $url;
    };
    $visibleFooterSections = collect($footerSections ?? [])->reject(fn ($section) => $section->title_en === 'Contact Info');
@endphp

<footer class="footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            @if ($siteSettings?->logo_path)
                <img src="{{ asset('storage/'.$siteSettings->logo_path) }}" alt="{{ $siteSettings?->{'hospital_name_'.$locale} ?? config('app.name') }}">
            @endif

        </div>

        <div class="footer-grid">
            @foreach ($visibleFooterSections as $section)
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
        <a class="developer-credit" href="https://wa.me/8801711090660" target="_blank" rel="noopener">Developed by</a>
    </div>
</footer>
