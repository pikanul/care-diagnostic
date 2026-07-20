@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isBn = $locale === 'bn';
    $embeddedSectionKeys = [
        'trust-highlights',
        'quick-action-panel',
        'statistics',
        'home-sample-collection',
        'physiotherapy-services',
        'health-packages',
        'testimonials',
        'latest-articles',
    ];
@endphp

@section('content')
    <style>
        .homepage-wrap { display: grid; gap: 16px; padding: 0; overflow: hidden; }
        .section-block { border-radius: 10px; overflow: hidden; border: 1px solid #dfe7f4; background: #fff; }
        .section-block#hero-slider { border: 0; border-radius: 0; margin-left: calc((100vw - 100%) / -2); margin-right: calc((100vw - 100%) / -2); }
        .section-block#hero-slider .section-inner { padding: 0; }
        .section-inner { padding: 20px; min-width: 0; }
        .section-head { display: flex; justify-content: space-between; gap: 18px; align-items: end; margin-bottom: 16px; }
        .section-kicker { color: var(--accent); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0; }
        .section-title { margin: 0; font-size: clamp(24px, 3vw, 36px); line-height: 1.15; overflow-wrap: anywhere; }
        .section-subtitle { margin: 8px 0 0; color: var(--muted); line-height: 1.6; overflow-wrap: anywhere; }
        .section-grid { display: grid; gap: 14px; }
        .cards-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .cards-3 { grid-template-columns: repeat(auto-fit, minmax(145px, 1fr)); }
        .cards-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .card-item, .stats-item, .point-item, .action-item { border: 1px solid var(--line); border-radius: 10px; background: #fff; padding: 16px; min-width: 0; box-shadow: 0 8px 22px rgba(16,35,61,.04); }
        .muted-text { color: var(--muted); overflow-wrap: anywhere; }
        .hero-shell { display: grid; gap: 16px; }
        .hero-slider { position: relative; height: clamp(720px, 51vw, 915px); outline: none; background: #eef7ff; overflow:hidden; }
        .hero-slider::before { content:""; position:absolute; inset:0; background:linear-gradient(90deg, rgba(255,255,255,.96) 0%, rgba(255,255,255,.8) 41%, rgba(255,255,255,.18) 63%, rgba(255,255,255,0) 100%); pointer-events:none; z-index:1; }
        .hero-stage { position: relative; height: 100%; overflow: hidden; border-radius: 0; background: transparent; z-index:2; }
        .hero-slide { position: absolute; inset: 0; display: grid; grid-template-columns: 1fr; gap: 0; opacity: 0; pointer-events: none; transition: opacity .35s ease, transform .35s ease; transform: translateX(10px); width: 100%; margin: 0; left: 0; right: 0; }
        .hero-slide.is-active { opacity: 1; pointer-events: auto; }
        .hero-slide.is-active { transform: translateX(0); }
        .hero-bg { position:absolute; inset:0; z-index:0; }
        .hero-bg picture,
        .hero-bg img { width:100%; height:100%; display:block; }
        .hero-bg img { object-fit:cover; object-position:center center; transform:scale(1.045); transform-origin:left center; }
        .hero-copy { display: grid; align-content: start; gap: 18px; width:min(1680px, calc(100% - 104px)); margin:0 auto; padding: 96px 0 196px; color: #07194a; text-align: var(--hero-text-align, left); background: transparent; position:relative; z-index:3; }
        .hero-copy .section-title { margin:0; color: #07194a !important; font-size: clamp(40px, 4.2vw, 68px); max-width: 760px; line-height:1.1; letter-spacing:0; font-weight:900; overflow-wrap:normal; }
        .hero-title-accent { display:block; color:#135ad7; }
        .hero-copy .section-subtitle { color: #26375f; max-width: 610px; font-size: clamp(16px, 1.18vw, 22px); line-height: 1.55; margin:0; }
        .hero-media { display:none; background: transparent; place-items: end center; padding: 24px 0 54px 10px; min-width:0; }
        .hero-media picture { align-self:stretch; display:grid !important; place-items:end center; width:100%; }
        .hero-media img, .hero-media video { width: 100%; height: 100%; object-fit: contain; object-position:center bottom; border-radius: 0; background: transparent; filter: drop-shadow(0 18px 26px rgba(30,58,91,.06)); }
        .hero-actions { display: flex; gap: 16px; flex-wrap: wrap; align-items:center; }
        .hero-actions .action-link { min-height: 52px; padding: 0 20px; border-radius:9px; font-size:16px; font-weight:900; border-color:#d6e2f1; box-shadow:0 10px 22px rgba(15,44,83,.08); }
        .hero-actions .action-link.primary { padding:0 22px; background:#0a9345; border-color:#0a9345; box-shadow:0 12px 24px rgba(10,147,69,.22); }
        .hero-actions .action-link:hover { transform:translateY(-1px); box-shadow:0 16px 28px rgba(15,44,83,.13); }
        .hero-badge { width: fit-content; display: inline-flex; align-items: center; gap: 8px; padding: 9px 14px; border-radius: 999px; background: #d9f4e6; color: #08773f; font-size: 14px; font-weight: 900; }
        .hero-badge::before { content:"☆"; font-size:20px; line-height:1; }
        .hero-trust-row { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 20px; margin-top: 12px; max-width: 720px; }
        .hero-trust-item { display:grid; grid-template-columns:auto 1fr; gap:9px; align-items:center; color:#07194a; font-size:13px; min-width:0; }
        .icon-ring { width:42px; height:42px; border:2px solid #075bd8; color:#075bd8; border-radius:10px; display:grid; place-items:center; font-weight:900; background:rgba(255,255,255,.4); }
        .icon-ring svg { width:28px; height:28px; stroke:currentColor; fill:none; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round; }
        .hero-trust-item strong { display:block; font-size:13px; line-height:1.12; font-weight:900; color:#07194a; }
        .hero-trust-item span span { display:block; color:#24385f; font-size:11px; margin-top:5px; line-height:1.25; }
        .quick-panel { position:absolute; left:50%; bottom:42px; transform:translateX(-50%); z-index:5; width:min(1668px, calc(100% - 120px)); display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); background:rgba(255,255,255,.96); border:1px solid #dfe7f4; box-shadow:0 16px 34px rgba(20,48,89,.14); border-radius:20px; overflow:hidden; backdrop-filter:blur(8px); }
        .quick-panel a { min-height:116px; padding:22px 34px; text-decoration:none; display:grid; grid-template-columns:auto 1fr; gap:18px; align-items:center; border-right:1px solid #d5dfec; transition:background .18s ease, transform .18s ease; }
        .quick-panel a:hover { background:#f7fbff; }
        .quick-panel a:last-child { border-right:0; }
        .quick-panel .icon-ring { width:56px; height:56px; border-radius:8px; background:#fff; }
        .quick-panel .icon-ring svg { width:38px; height:38px; }
        .quick-panel strong { display:block; font-size:19px; color:#07194a; line-height:1.12; font-weight:900; }
        .quick-panel span.quick-subtitle { display:block; color:#24385f; font-size:16px; line-height:1.2; margin-top:7px; }
        .hero-indicators { display: flex; gap: 8px; justify-content: center; padding-top: 14px; }
        .hero-indicators button { width: 11px; height: 11px; border-radius: 999px; border: 0; background: #c9d5ea; cursor: pointer; }
        .hero-indicators button.is-active { background: var(--accent); }
        .highlight-strip { display:grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap:0; padding:18px 20px; color:#fff; background:#003a86; border:1px solid #0b4a9b; border-radius:12px; min-width: 0; }
        .highlight-item { padding: 4px 14px; border-right: 1px solid rgba(255,255,255,.18); min-width: 0; }
        .highlight-item:last-child { border-right: 0; }
        .highlight-item strong { display:block; color:#fff; font-size: 15px; line-height: 1.25; }
        .highlight-item span { display:block; color:rgba(255,255,255,.82); font-size: 12px; line-height: 1.35; margin-top: 4px; }
        .stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .stat-value { font-size: 30px; font-weight: 700; color: var(--text); }
        .section-cta { display:flex; justify-content:space-between; gap:20px; align-items:center; padding:22px; background:#10233d; color:#fff; border-radius:14px; min-width: 0; }
        .section-cta .section-subtitle { color: rgba(255,255,255,.82); }
        .hero-placeholder { width: 100%; min-height: 320px; border-radius: 12px; background: radial-gradient(circle at 50% 35%, #ffffff 0 22%, #d7e9fb 23% 42%, #eef8f4 43% 100%); display: grid; place-items: center; font-weight: 700; color: transparent; }
        .sample-note { margin-top: 10px; font-size: 12px; color: var(--muted); }
        .card-image { display:block; width:100%; aspect-ratio: 1.5 / 1; object-fit: cover; border-radius: 8px 8px 0 0; margin: -16px -16px 12px; max-width: calc(100% + 32px); }
        .service-dot { width: 42px; height: 42px; border-radius: 999px; display: grid; place-items: center; color: #fff; background: var(--card-accent, var(--accent)); font-weight: 800; margin: -34px auto 10px; position: relative; box-shadow: 0 8px 18px rgba(17, 36, 63, .16); }
        .card-item h3 { color:#07194a; text-align:center; font-size:16px; line-height:1.25; }
        .card-item .muted-text { text-align:center; font-size:12px; line-height:1.55; }
        .carousel-track { display:grid; gap:16px; }
        .slide-nav { position:absolute; right: 18px; bottom: 18px; display:flex; gap:8px; z-index: 2; }
        .slide-nav button { border:0; border-radius:8px; background:rgba(255,255,255,.92); padding:10px 12px; cursor:pointer; }
        .doctor-carousel { position: relative; display: grid; gap: 14px; overflow: hidden; }
        .doctor-viewport { overflow-x: auto; overflow-y: hidden; scroll-snap-type: x mandatory; overscroll-behavior-x: contain; scrollbar-width: none; padding-bottom: 4px; }
        .doctor-viewport::-webkit-scrollbar { display: none; }
        .doctor-track { display: flex; gap: 16px; min-width: 100%; }
        .doctor-card-item { flex: 0 0 calc((100% - (16px * (var(--doctor-desktop-cards) - 1))) / var(--doctor-desktop-cards)); max-width: 100%; scroll-snap-align: start; border: 1px solid var(--line); border-radius: 10px; overflow: hidden; background: #fff; display: grid; min-width: 0; text-align:center; box-shadow: 0 8px 22px rgba(16,35,61,.04); }
        .doctor-photo { position: relative; background: #fff; padding: 16px 16px 0; display:grid; place-items:center; }
        .doctor-photo img { display: block; width: 88px; height: 88px; object-fit: cover; border-radius:999px; background:#eef4f8; }
        .doctor-placeholder { width: 88px; height: 88px; border-radius:999px; display: grid; place-items: center; background: radial-gradient(circle at 50% 30%, #fff 0 18%, #dfe9f6 19% 38%, #eef8f4 39% 100%); font-weight: 700; color: transparent; }
        .test-category-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px; }
        .test-category { border-right: 1px solid var(--line); padding-right: 12px; min-width: 0; }
        .test-category:last-child { border-right: 0; }
        .test-category h3 { margin: 0 0 10px; font-size: 16px; }
        .test-category ul { margin: 0; padding-left: 18px; color: var(--text); line-height: 1.8; }
        .section-visual { display:block; width:100%; border-radius:10px; object-fit:cover; max-height:230px; }
        .why-layout { display:grid; grid-template-columns: 1.05fr .82fr 1.45fr .95fr; gap:18px; align-items:stretch; }
        .why-panel { padding:18px; border-right:1px solid #e7edf7; }
        .why-panel ul { list-style:none; padding:0; margin:16px 0 0; display:grid; gap:8px; }
        .why-panel li { font-size:13px; color:#1a2d55; }
        .why-panel li::before { content:"✓"; display:inline-grid; place-items:center; width:16px; height:16px; border-radius:999px; margin-right:8px; background:#09944b; color:#fff; font-size:11px; }
        .why-stats-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; align-content:center; }
        .why-stat { border-radius:9px; padding:20px 16px; color:#fff; background:#058744; min-height:96px; display:grid; align-content:center; }
        .why-stat:nth-child(even) { background:#0049b6; }
        .why-stat strong { font-size:30px; line-height:1; }
        .why-building { display:grid; gap:10px; }
        .why-building img { width:100%; height:190px; object-fit:cover; border-radius:10px; }
        .building-tags { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:8px; font-size:11px; color:#075bd8; text-align:center; }
        .home-sample-card { border:1px solid #e2e9f4; border-radius:10px; padding:18px; display:grid; grid-template-columns:1fr auto; gap:12px; overflow:hidden; }
        .home-sample-card img { width:120px; align-self:end; }
        .facility-row { display:grid; grid-template-columns:repeat(7,minmax(0,1fr)); gap:0; text-align:center; }
        .facility-row .card-item { box-shadow:none; border:0; border-right:1px solid #e6edf6; border-radius:0; padding:12px 8px; }
        .facility-row .card-item:last-child { border-right:0; }
        .cta-image { align-self:stretch; min-width:160px; max-width:260px; display:grid; place-items:end; }
        .cta-image img { width:100%; height:100%; max-height:180px; object-fit:contain; display:block; }
        .doctor-badge { position: absolute; left: 12px; top: 12px; padding: 4px 8px; border-radius: 999px; background: #10233d; color: #fff; font-size: 11px; font-weight: 700; }
        .doctor-body { display: grid; gap: 5px; padding: 12px 14px 16px; }
        .doctor-body h3 { margin: 0; font-size: 15px; line-height: 1.25; overflow-wrap: anywhere; color:#07194a; }
        .doctor-body .doctor-meta strong { display:none; }
        .doctor-meta { margin: 0; color: var(--muted); line-height: 1.45; overflow-wrap: anywhere; font-size:12px; }
        .doctor-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
        .doctor-nav { position: absolute; top: 50%; transform: translateY(-50%); border: 0; border-radius: 999px; width: 42px; height: 42px; background: rgba(255,255,255,.94); box-shadow: 0 8px 24px rgba(16,35,61,.12); z-index: 2; cursor: pointer; }
        .doctor-prev { left: 6px; }
        .doctor-next { right: 6px; }
        .doctor-dots { display: flex; justify-content: center; gap: 8px; }
        .doctor-dots button { width: 10px; height: 10px; border-radius: 999px; border: 0; background: #c9d5ea; }
        .doctor-dots button.is-active { background: var(--accent); }
        .physio-services-grid { display: grid; grid-template-columns: repeat(var(--physio-desktop-columns), minmax(0, 1fr)); gap: 14px; width: 100%; overflow: hidden; }
        .physio-service-card { border: 1px solid var(--line); border-radius: 14px; background: #fff; padding: 16px; display: grid; gap: 12px; min-width: 0; }
        .physio-service-card.is-featured { border-color: rgba(15,118,110,.32); box-shadow: 0 10px 24px rgba(15,118,110,.08); }
        .physio-service-card h3 { margin: 8px 0 6px; font-size: 18px; line-height: 1.25; overflow-wrap: anywhere; }
        .physio-icon { width: 38px; height: 38px; border-radius: 10px; display: grid; place-items: center; background: #ecf8f6; color: var(--accent); font-weight: 800; font-size: 22px; }

        @media (max-width: 960px) {
            .cards-2, .cards-3, .cards-4, .stats-grid, .test-category-grid, .highlight-strip, .why-layout, .facility-row { grid-template-columns: 1fr; }
            .test-category { border-right: 0; border-bottom: 1px solid var(--line); padding: 0 0 12px; }
            .test-category:last-child { border-bottom: 0; }
            .physio-services-grid { grid-template-columns: repeat(var(--physio-tablet-columns), minmax(0, 1fr)); }
            .hero-slider { height:auto; min-height: 0; }
            .hero-stage { height:auto; min-height: 0; }
            .hero-slider::before { background:linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.64)); }
            .hero-slide { position: relative; grid-template-columns: 1fr; opacity: 1; pointer-events: auto; transform: none; margin-bottom: 14px; width:100%; min-height:720px; }
            .hero-bg img { object-position: 62% center; transform:none; }
            .quick-panel { position:static; transform:none; width:min(100% - 24px, 1180px); grid-template-columns:1fr 1fr; margin:0 auto 14px; border-radius:16px; }
            .quick-panel a { border-right:0; border-bottom:1px solid #e7edf7; }
            .hero-trust-row { grid-template-columns:1fr 1fr; }
            .hero-media { min-height: 300px; padding: 0 12px 0; order:-1; }
            .hero-copy { width:min(100% - 32px, 720px); padding: 42px 0 20px; }
            .hero-copy, .hero-media { border-radius: 12px; }
            .highlight-strip, .section-cta, .section-head { flex-direction: column; align-items: flex-start; }
            .highlight-item { border-right: 0; border-bottom: 1px solid rgba(255,255,255,.18); padding: 10px 0; }
            .highlight-item:last-child { border-bottom: 0; }
            .cta-image { max-width: 100%; width: 100%; justify-items: center; }
            .doctor-card-item { flex-basis: calc((100% - 16px) / var(--doctor-mobile-cards)); }
            .doctor-nav { display: none; }
        }

        @media (max-width: 560px) {
            .homepage-wrap { gap: 14px; padding-top: 14px; }
            .section-block { border-radius: 10px; }
            .section-inner { padding: 14px; }
            .section-title { font-size: 23px; }
            .section-subtitle { font-size: 15px; }
            .hero-slide { min-height:760px; }
            .hero-bg img { object-position: 65% center; transform:none; }
            .hero-copy { padding: 28px 0 18px; }
            .hero-copy .section-title { font-size: 28px; }
            .hero-copy .section-subtitle { font-size: 14px; }
            .hero-badge { font-size:12px; padding:7px 10px; }
            .hero-trust-row { grid-template-columns:1fr; }
            .hero-trust-item strong { font-size:13px; }
            .hero-trust-item span span { font-size:11px; }
            .quick-panel { grid-template-columns:1fr; }
            .quick-panel a { min-height:86px; padding:16px; gap:14px; }
            .quick-panel .icon-ring { width:48px; height:48px; }
            .quick-panel strong { font-size:15px; }
            .quick-panel span.quick-subtitle { font-size:13px; margin-top:4px; }
            .home-sample-card { grid-template-columns:1fr; }
            .hero-placeholder { min-height: 220px; }
            .hero-media { min-height: 210px; }
            .hero-actions .action-link,
            .doctor-actions .action-link,
            .section-cta .action-link {
                width: 100%;
            }
            .doctor-track { gap: 12px; }
            .doctor-card-item { flex-basis: calc((100% - 12px) / var(--doctor-mobile-cards)); }
            .physio-services-grid { grid-template-columns: repeat(var(--physio-mobile-columns), minmax(0, 1fr)); }
        }

        @media (max-width: 360px) {
            .section-inner { padding: 12px; }
            .section-title { font-size: 21px; }
            .hero-copy .section-title { font-size: 25px; }
            .card-item, .stats-item, .point-item, .action-item, .physio-service-card { padding: 12px; }
            .hero-copy { padding: 16px; }
        }

    </style>

    <div class="container homepage-wrap">
        @foreach ($homepageSections as $section)
            @if (! in_array($section->section_key, $embeddedSectionKeys, true))
                @include('partials.public.home-section', ['section' => $section, 'isBn' => $isBn])
            @endif
        @endforeach
    </div>

    <script>
        document.querySelectorAll('[data-hero-slider]').forEach((slider) => {
            const slides = Array.from(slider.querySelectorAll('[data-slide]'));
            const indicators = Array.from(slider.querySelectorAll('[data-indicator]'));
            const prev = slider.querySelector('[data-prev]');
            const next = slider.querySelector('[data-next]');
            const autoPlay = slider.dataset.autoPlay === '1';
            const pauseOnHover = slider.dataset.pauseOnHover === '1';
            const swipe = slider.dataset.swipe === '1';
            const keyboard = slider.dataset.keyboard === '1';
            const speed = parseInt(slider.dataset.speed || '4500', 10);
            const lazyLoad = slider.dataset.lazyLoad === '1';
            let index = 0;
            let timer = null;
            let touchStartX = null;

            if (! slides.length) {
                return;
            }

            const loadSlide = (slide) => {
                if (! slide || slide.dataset.loaded === '1') {
                    return;
                }

                slide.querySelectorAll('img[data-src]').forEach((img) => {
                    if (! img.getAttribute('src') || img.getAttribute('src').startsWith('data:image')) {
                        img.src = img.dataset.src;
                    }
                });

                slide.querySelectorAll('source[data-srcset]').forEach((source) => {
                    source.srcset = source.dataset.srcset;
                });

                slide.querySelectorAll('video[data-src]').forEach((video) => {
                    if (! video.getAttribute('src')) {
                        video.src = video.dataset.src;
                        video.load();
                    }
                });

                slide.dataset.loaded = '1';
            };

            const show = (nextIndex) => {
                index = (nextIndex + slides.length) % slides.length;
                slides.forEach((slide, i) => {
                    const active = i === index;
                    slide.classList.toggle('is-active', active);
                    slide.setAttribute('aria-hidden', active ? 'false' : 'true');
                    if (active || ! lazyLoad) {
                        loadSlide(slide);
                    }
                });
                indicators.forEach((indicator, i) => {
                    const active = i === index;
                    indicator.classList.toggle('is-active', active);
                    indicator.setAttribute('aria-current', active ? 'true' : 'false');
                });
            };

            const startAutoplay = () => {
                if (! autoPlay || slides.length <= 1) {
                    return;
                }

                stopAutoplay();
                timer = window.setInterval(() => show(index + 1), speed);
            };

            const stopAutoplay = () => {
                if (timer) {
                    window.clearInterval(timer);
                    timer = null;
                }
            };

            show(0);
            startAutoplay();

            prev?.addEventListener('click', () => {
                show(index - 1);
                startAutoplay();
            });

            next?.addEventListener('click', () => {
                show(index + 1);
                startAutoplay();
            });

            indicators.forEach((indicator, i) => {
                indicator.addEventListener('click', () => {
                    show(i);
                    startAutoplay();
                });
            });

            if (pauseOnHover) {
                slider.addEventListener('mouseenter', stopAutoplay);
                slider.addEventListener('mouseleave', startAutoplay);
                slider.addEventListener('focusin', stopAutoplay);
                slider.addEventListener('focusout', startAutoplay);
            }

            if (keyboard) {
                slider.addEventListener('keydown', (event) => {
                    if (event.key === 'ArrowLeft') {
                        event.preventDefault();
                        show(index - 1);
                        startAutoplay();
                    }

                    if (event.key === 'ArrowRight') {
                        event.preventDefault();
                        show(index + 1);
                        startAutoplay();
                    }
                });
            }

            if (swipe && 'ontouchstart' in window) {
                slider.addEventListener('touchstart', (event) => {
                    touchStartX = event.touches[0].clientX;
                }, { passive: true });

                slider.addEventListener('touchend', (event) => {
                    if (touchStartX === null) {
                        return;
                    }

                    const touchEndX = event.changedTouches[0].clientX;
                    const delta = touchEndX - touchStartX;
                    if (Math.abs(delta) > 40) {
                        show(delta > 0 ? index - 1 : index + 1);
                        startAutoplay();
                    }

                    touchStartX = null;
                }, { passive: true });
            }
        });

        document.querySelectorAll('[data-doctor-carousel]').forEach((carousel) => {
            const viewport = carousel.querySelector('.doctor-viewport');
            const track = carousel.querySelector('[data-doctor-track]');
            const prev = carousel.querySelector('[data-doctor-prev]');
            const next = carousel.querySelector('[data-doctor-next]');
            const dots = Array.from(carousel.querySelectorAll('[data-doctor-dot]'));
            const autoScroll = carousel.dataset.autoScroll === '1';
            const pauseOnHover = carousel.dataset.pauseOnHover === '1';
            const loop = carousel.dataset.loop === '1';
            const speed = parseInt(carousel.dataset.speed || '4500', 10);
            let timer = null;
            let activeIndex = 0;

            if (! viewport || ! track) {
                return;
            }

            const cards = Array.from(track.children);
            if (! cards.length) {
                return;
            }

            const getStep = () => {
                const cardWidth = cards[0].getBoundingClientRect().width;
                const gap = parseFloat(getComputedStyle(track).gap || '16');
                return cardWidth + gap;
            };

            const visibleCards = () => {
                const cardWidth = cards[0].getBoundingClientRect().width + parseFloat(getComputedStyle(track).gap || '16');
                return Math.max(1, Math.round(viewport.clientWidth / cardWidth));
            };

            const updateDots = () => {
                if (! dots.length) {
                    return;
                }

                const current = Math.min(dots.length - 1, Math.round(viewport.scrollLeft / getStep()));
                activeIndex = current;
                dots.forEach((dot, index) => {
                    const active = index === current;
                    dot.classList.toggle('is-active', active);
                    dot.setAttribute('aria-current', active ? 'true' : 'false');
                });
            };

            const scrollToIndex = (index) => {
                const maxIndex = Math.max(0, cards.length - visibleCards());
                const target = loop ? (index + cards.length) % cards.length : Math.max(0, Math.min(index, maxIndex));
                viewport.scrollTo({ left: getStep() * target, behavior: 'smooth' });
                activeIndex = target;
                updateDots();
            };

            const stop = () => {
                if (timer) {
                    window.clearInterval(timer);
                    timer = null;
                }
            };

            const start = () => {
                if (! autoScroll || cards.length <= visibleCards()) {
                    return;
                }

                stop();
                timer = window.setInterval(() => {
                    const maxIndex = Math.max(0, cards.length - visibleCards());
                    if (! loop && activeIndex >= maxIndex) {
                        stop();
                        return;
                    }

                    scrollToIndex(activeIndex + 1);
                }, speed);
            };

            prev?.addEventListener('click', () => {
                scrollToIndex(activeIndex - 1);
                start();
            });

            next?.addEventListener('click', () => {
                scrollToIndex(activeIndex + 1);
                start();
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    scrollToIndex(index);
                    start();
                });
            });

            viewport.addEventListener('scroll', () => {
                window.requestAnimationFrame(updateDots);
            });

            if (pauseOnHover) {
                carousel.addEventListener('mouseenter', stop);
                carousel.addEventListener('mouseleave', start);
                carousel.addEventListener('focusin', stop);
                carousel.addEventListener('focusout', start);
            }

            window.addEventListener('resize', updateDots);
            updateDots();
            start();
        });
    </script>
@endsection
