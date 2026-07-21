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
        .hero-slider { position: relative; width:100vw; height:min(56.25vw, calc(100vh - 128px)); min-height:520px; outline: none; background: #eaf6ff; overflow:hidden; }
        .hero-slider::before { display:none; }
        .hero-stage { position: relative; height: 100%; overflow: hidden; border-radius: 0; background: transparent; z-index:2; }
        .hero-slide { position: absolute; inset: 0; display: grid; grid-template-columns: 1fr; gap: 0; opacity: 0; pointer-events: none; transition: opacity .35s ease, transform .35s ease; transform: translateX(10px); width: 100%; margin: 0; left: 0; right: 0; }
        .hero-slide.is-active { opacity: 1; pointer-events: auto; }
        .hero-slide.is-active { transform: translateX(0); }
        .hero-bg { position:absolute; inset:0; z-index:0; }
        .hero-bg picture,
        .hero-bg img { width:100%; height:100%; display:block; }
        .hero-bg img { object-fit:cover; object-position:center center; transform:none; transform-origin:center center; }
        .hero-copy { display: grid; align-content: start; gap: 20px; width:min(1680px, calc(100% - 104px)); margin:0 auto; padding: 96px 0 210px; color: #07194a; text-align: var(--hero-text-align, left); background: transparent; position:relative; z-index:3; }
        .hero-copy .section-title { margin:0; color: #082352 !important; font-size: clamp(44px, 4.72vw, 78px); max-width: 830px; line-height:1.08; letter-spacing:0; font-weight:900; overflow-wrap:normal; }
        .hero-title-accent { display:block; color:#075fc7; }
        .hero-copy .section-subtitle { color: #244b7d; max-width: 670px; font-size: clamp(17px, 1.3vw, 24px); line-height: 1.55; margin:0; }
        .hero-media { display:none; background: transparent; place-items: end center; padding: 24px 0 54px 10px; min-width:0; }
        .hero-media picture { align-self:stretch; display:grid !important; place-items:end center; width:100%; }
        .hero-media img, .hero-media video { width: 100%; height: 100%; object-fit: contain; object-position:center bottom; border-radius: 0; background: transparent; filter: drop-shadow(0 18px 26px rgba(30,58,91,.06)); }
        .hero-actions { display: flex; gap: 16px; flex-wrap: wrap; align-items:center; }
        .hero-actions .action-link { min-height: 58px; padding: 0 24px; border-radius:10px; font-size:17px; font-weight:900; border-color:#d6e2f1; box-shadow:0 10px 22px rgba(15,44,83,.08); }
        .hero-actions .action-link.primary { padding:0 26px; background:linear-gradient(135deg,#004aa5,#0bb7c7); border-color:#0b66c3; box-shadow:0 12px 24px rgba(0,74,165,.22); }
        .hero-actions .action-link:hover { transform:translateY(-1px); box-shadow:0 16px 28px rgba(15,44,83,.13); }
        .hero-badge { width: fit-content; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 999px; background: #d9f2ff; color: #0061b5; font-size: 16px; font-weight: 900; }
        .hero-badge::before { content:"☆"; font-size:22px; line-height:1; }
        .hero-trust-row { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 22px; margin-top: 14px; max-width: 810px; }
        .hero-trust-item { display:grid; grid-template-columns:auto 1fr; gap:10px; align-items:center; color:#07194a; font-size:14px; min-width:0; }
        .icon-ring { width:48px; height:48px; border:2px solid #0b66c3; color:#0b66c3; border-radius:10px; display:grid; place-items:center; font-weight:900; background:rgba(255,255,255,.54); }
        .icon-ring svg { width:31px; height:31px; stroke:currentColor; fill:none; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round; }
        .hero-trust-item strong { display:block; font-size:14px; line-height:1.12; font-weight:900; color:#07194a; }
        .hero-trust-item span span { display:block; color:#24385f; font-size:12px; margin-top:5px; line-height:1.25; }
        .quick-panel { position:absolute; right:24px; bottom:24px; z-index:5; width:min(820px, calc(100% - 48px)); display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); background:rgba(255,255,255,.56); border:1px solid rgba(255,255,255,.74); box-shadow:0 18px 38px rgba(0,74,165,.14), inset 0 1px 0 rgba(255,255,255,.76); border-radius:16px; overflow:hidden; backdrop-filter:blur(15px) saturate(1.18); -webkit-backdrop-filter:blur(15px) saturate(1.18); }
        .quick-panel a { position:relative; isolation:isolate; min-height:62px; padding:10px 12px; text-decoration:none; display:grid; grid-template-columns:auto minmax(0,1fr); gap:9px; align-items:center; border-right:1px solid rgba(11,102,195,.14); overflow:hidden; transition:color .18s ease, transform .18s ease, box-shadow .18s ease; }
        .quick-panel a::before { content:""; position:absolute; width:120px; height:120px; left:var(--quick-x, 50%); top:var(--quick-y, 50%); border-radius:999px; background:radial-gradient(circle, rgba(11,183,199,.34) 0%, rgba(0,74,165,.18) 38%, rgba(255,255,255,0) 70%); opacity:0; transform:translate(-50%, -50%) scale(.45); pointer-events:none; z-index:-1; }
        .quick-panel a::after { content:""; position:absolute; inset:8px; border-radius:14px; background:linear-gradient(135deg, rgba(255,255,255,.58), rgba(11,183,199,.10)); opacity:0; transform:scale(.96); transition:opacity .18s ease, transform .18s ease; pointer-events:none; z-index:-2; }
        .quick-panel a:hover, .quick-panel a:focus-visible { color:#004aa5; transform:translateY(-3px); box-shadow:0 14px 24px rgba(0,74,165,.10); outline:0; }
        .quick-panel a:hover::after, .quick-panel a:focus-visible::after { opacity:1; transform:scale(1); }
        .quick-panel a:active { transform:translateY(-1px) scale(.99); }
        .quick-panel a.is-pressing::before { animation:quick-pulse .45s ease; }
        .quick-panel a:last-child { border-right:0; }
        .quick-panel .icon-ring { width:34px; height:34px; border-radius:9px; background:rgba(255,255,255,.78); box-shadow:0 8px 16px rgba(0,74,165,.08); transition:background .18s ease, transform .18s ease, box-shadow .18s ease; }
        .quick-panel a:hover .icon-ring, .quick-panel a:focus-visible .icon-ring { background:#fff; transform:scale(1.06) rotate(-2deg); box-shadow:0 12px 20px rgba(0,74,165,.16); }
        .quick-panel .icon-ring svg { width:22px; height:22px; }
        .quick-panel strong { display:block; font-size:13px; color:#07194a; line-height:1.1; font-weight:900; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .quick-panel span.quick-subtitle { display:block; color:#24385f; font-size:11px; line-height:1.15; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        @keyframes quick-pulse {
            0% { opacity:.84; transform:translate(-50%, -50%) scale(.35); }
            100% { opacity:0; transform:translate(-50%, -50%) scale(1.35); }
        }
        .hero-indicators { display: flex; gap: 8px; justify-content: center; padding-top: 14px; }
        .hero-indicators button { width: 11px; height: 11px; border-radius: 999px; border: 0; background: #c9d5ea; cursor: pointer; }
        .hero-indicators button.is-active { background: var(--accent); }
        .highlight-strip { display:grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap:0; padding:18px 20px; color:#fff; background:linear-gradient(135deg,#004aa5,#0571bd); border:1px solid #0b66c3; border-radius:12px; min-width: 0; }
        .highlight-item { padding: 4px 14px; border-right: 1px solid rgba(255,255,255,.18); min-width: 0; }
        .highlight-item:last-child { border-right: 0; }
        .highlight-item strong { display:block; color:#fff; font-size: 15px; line-height: 1.25; }
        .highlight-item span { display:block; color:rgba(255,255,255,.82); font-size: 12px; line-height: 1.35; margin-top: 4px; }
        .stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .stat-value { font-size: 30px; font-weight: 700; color: var(--text); }
        .section-cta { display:flex; justify-content:space-between; gap:20px; align-items:center; padding:22px; background:linear-gradient(135deg,#003678,#0571bd); color:#fff; border-radius:14px; min-width: 0; }
        .section-cta .section-subtitle { color: rgba(255,255,255,.82); }
        .appointment-cta-card { min-height:96px; padding:14px 18px; gap:16px; border-radius:12px; }
        .appointment-cta-card .section-kicker { color:#d7ecff; font-size:10px; }
        .appointment-cta-card .section-title { font-size:clamp(19px,2vw,27px); line-height:1.1; }
        .appointment-cta-card .section-subtitle { max-width:520px; margin-top:5px; font-size:13px; line-height:1.45; }
        .appointment-cta-card .cta-actions { margin-left:auto; flex:0 0 auto; }
        .appointment-cta-card .action-link { min-height:52px; padding:0 24px; border-radius:10px; font-size:15px; white-space:nowrap; }
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
        .doctor-viewport { overflow-x: auto; overflow-y: hidden; scroll-snap-type: x proximity; overscroll-behavior-x: contain; scrollbar-width: none; padding-bottom: 4px; cursor: grab; touch-action: pan-y; }
        .doctor-viewport.is-dragging { cursor: grabbing; scroll-snap-type: none; }
        .doctor-viewport::-webkit-scrollbar { display: none; }
        .doctor-track { display: flex; gap: 16px; min-width: 100%; }
        .doctor-card-item { flex: 0 0 calc((100% - (16px * (var(--doctor-desktop-cards) - 1))) / var(--doctor-desktop-cards)); max-width: 100%; scroll-snap-align: start; border: 1px solid var(--line); border-radius: 10px; overflow: hidden; background: #fff; display: grid; min-width: 0; text-align:center; box-shadow: 0 8px 22px rgba(16,35,61,.04); user-select:none; }
        .doctor-photo { position: relative; background: #fff; padding: 14px 14px 0; display:grid; place-items:center; }
        .doctor-photo picture { display:block; width:min(52%, 150px); max-width:100%; }
        .doctor-photo img { display: block; width:100%; aspect-ratio:1 / 1; height:auto; object-fit: cover; border-radius:12px; background:#eef4f8; border:1px solid #dbe7f5; }
        .doctor-placeholder { width:min(52%, 150px); aspect-ratio:1 / 1; border-radius:12px; display: grid; place-items: center; background: radial-gradient(circle at 50% 30%, #fff 0 18%, #dfe9f6 19% 38%, #eef8f4 39% 100%); font-weight: 700; color: transparent; }
        .test-category-grid { display:flex; gap: 16px; overflow-x:auto; overflow-y:hidden; scroll-snap-type:x mandatory; scrollbar-width:none; overscroll-behavior-x:contain; padding:2px 2px 8px; touch-action:pan-y; }
        .test-category-grid::-webkit-scrollbar { display:none; }
        .test-category { flex:0 0 calc((100% - 64px) / 5); min-width:0; scroll-snap-align:start; border:1px solid var(--line); border-radius:10px; padding:16px; background:rgba(255,255,255,.86); box-shadow:0 8px 22px rgba(16,35,61,.04); }
        .test-category h3 { margin: 0 0 10px; font-size: 16px; color:#07194a; line-height:1.25; }
        .test-category ul { margin: 0; padding-left: 18px; color: var(--text); line-height: 1.65; font-size:13px; }
        .test-modal { position:fixed; inset:0; z-index:1000; display:none; align-items:center; justify-content:center; padding:24px; }
        .test-modal.is-open { display:flex; }
        .test-modal-backdrop { position:absolute; inset:0; background:rgba(5,20,48,.36); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); }
        .test-modal-card { position:relative; z-index:1; width:min(1120px, 100%); max-height:min(82vh, 820px); overflow:hidden; display:grid; grid-template-rows:auto 1fr; border:1px solid rgba(255,255,255,.64); border-radius:16px; background:rgba(255,255,255,.84); box-shadow:0 24px 70px rgba(0,38,88,.24); backdrop-filter:blur(18px) saturate(1.1); -webkit-backdrop-filter:blur(18px) saturate(1.1); }
        .test-modal-head { display:flex; justify-content:space-between; gap:18px; align-items:center; padding:18px 20px; border-bottom:1px solid rgba(11,102,195,.16); }
        .test-modal-head h3 { margin:4px 0 0; font-size:26px; line-height:1.15; color:#07194a; }
        .test-modal-head button { width:40px; height:40px; padding:0; border-radius:999px; font-size:30px; line-height:1; background:#004aa5; color:#fff; }
        .test-modal-body { overflow:auto; padding:20px; display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
        .test-modal-category { min-width:0; border:1px solid rgba(11,102,195,.13); border-radius:12px; padding:16px; background:rgba(255,255,255,.72); }
        .test-modal-category h4 { margin:0 0 10px; color:#004aa5; font-size:17px; line-height:1.25; }
        .test-modal-category ul { margin:0; padding-left:18px; line-height:1.65; color:#172033; font-size:13px; }
        .section-visual { display:block; width:100%; border-radius:10px; object-fit:cover; max-height:230px; }
        .section-block#why-choose-us { border:0; background:transparent; }
        .section-block#why-choose-us .section-inner { padding:0; }
        .why-layout { display:grid; grid-template-columns:minmax(260px,.82fr) minmax(280px,.92fr) minmax(500px,1.7fr); gap:22px; align-items:stretch; }
        .why-panel,
        .why-stats-panel,
        .why-building { border:1px solid #e1e8f2; border-radius:9px; background:#fff; box-shadow:0 10px 24px rgba(16,35,61,.035); min-width:0; }
        .why-panel { padding:24px 26px; display:grid; align-content:start; }
        .why-panel h3 { margin:8px 0 10px; color:#07194a; font-size:clamp(25px,2.1vw,34px); line-height:1.08; font-weight:900; letter-spacing:0; overflow-wrap:anywhere; }
        .why-panel .muted-text { color:#4f678c; font-size:15px; line-height:1.58; text-align:left; }
        .why-panel ul { list-style:none; padding:0; margin:18px 0 0; display:grid; gap:10px; }
        .why-panel li { display:grid; grid-template-columns:auto 1fr; align-items:start; gap:10px; font-size:14px; line-height:1.25; color:#1a2d55; min-width:0; }
        .why-panel li::before { content:"✓"; display:inline-grid; place-items:center; width:17px; height:17px; border-radius:999px; background:#09944b; color:#fff; font-size:11px; font-weight:900; margin-top:1px; }
        .why-stats-panel { padding:22px; display:grid; align-content:center; }
        .why-stats-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:22px; align-content:center; }
        .why-stat { border-radius:6px; padding:22px 20px; color:#fff; background:linear-gradient(135deg,#004aa5,#075fc7); min-height:112px; display:grid; align-content:center; gap:8px; box-shadow:0 10px 18px rgba(0,74,165,.12); }
        .why-stat:nth-child(even) { background:linear-gradient(135deg,#058744,#12a75b); }
        .why-stat:nth-child(3) { background:linear-gradient(135deg,#004aa5,#075fc7); }
        .why-stat:nth-child(4) { background:linear-gradient(135deg,#058744,#12a75b); }
        .why-stat strong { font-size:30px; line-height:1; font-weight:900; color:#fff; }
        .why-stat span { font-size:15px; line-height:1.22; color:rgba(255,255,255,.92); font-weight:700; overflow-wrap:anywhere; }
        .why-building { overflow:hidden; display:grid; grid-template-rows:minmax(210px,1fr) auto; }
        .why-building img { width:100%; height:100%; min-height:210px; max-height:270px; object-fit:cover; border-radius:0; display:block; }
        .building-tags { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; padding:14px 16px; background:#fff; border-top:1px solid #e6edf6; }
        .building-tags a { display:grid; grid-template-columns:auto 1fr; gap:7px; align-items:center; justify-content:center; min-height:42px; padding:6px 8px; border:1px solid #dce8f5; border-radius:8px; background:#f8fbff; color:#0b57bd; font-size:11px; line-height:1.12; font-weight:850; text-align:left; text-decoration:none; min-width:0; transition:background .16s ease, border-color .16s ease, transform .16s ease; }
        .building-tags a:hover { background:#edf8ff; border-color:#0b66c3; transform:translateY(-1px); }
        .building-tags a::before { content:""; width:22px; height:22px; border:2px solid #0b66c3; border-radius:6px; background:linear-gradient(135deg,rgba(11,102,195,.08),rgba(11,183,199,.08)); }
        .home-sample-card { border:1px solid #e2e9f4; border-radius:10px; padding:18px; display:grid; grid-template-columns:1fr auto; gap:12px; overflow:hidden; }
        .home-sample-card img { width:120px; align-self:end; }
        .facility-row { display:grid; grid-template-columns:repeat(7,minmax(0,1fr)); gap:0; text-align:center; }
        .facility-row .card-item { box-shadow:none; border:0; border-right:1px solid #e6edf6; border-radius:0; padding:12px 8px; }
        .facility-row .card-item:last-child { border-right:0; }
        .cta-image { align-self:stretch; min-width:160px; max-width:260px; display:grid; place-items:end; }
        .cta-image img { width:100%; height:100%; max-height:180px; object-fit:contain; display:block; }
        .doctor-badge { position: absolute; left: 12px; top: 12px; padding: 4px 8px; border-radius: 999px; background: #004aa5; color: #fff; font-size: 11px; font-weight: 700; }
        .doctor-body { display: grid; gap: 5px; padding: 12px 14px 16px; }
        .doctor-body h3 { margin: 0; font-size: 15px; line-height: 1.25; overflow-wrap: anywhere; color:#07194a; }
        .doctor-body .doctor-meta strong { display:none; }
        .doctor-meta { margin: 0; color: var(--muted); line-height: 1.45; overflow-wrap: anywhere; font-size:12px; }
        .doctor-actions { display: grid; gap: 8px; margin-top: 6px; }
        .doctor-actions .action-link { width:100%; min-height:42px; padding:8px 10px; border-radius:9px; font-size:13px; }
        .doctor-actions .doctor-book-call { background:linear-gradient(135deg,#004aa5,#0bb7c7); color:#fff; border-color:#0b66c3; box-shadow:0 8px 18px rgba(0,74,165,.18); }
        .doctor-call-number { display:none; color:#0b57bd; font-weight:900; font-size:13px; line-height:1.2; }
        .doctor-card-item.is-number-visible .doctor-call-number { display:block; }
        .doctor-nav { position: absolute; top: 50%; transform: translateY(-50%); border: 0; border-radius: 999px; width: 42px; height: 42px; background: rgba(255,255,255,.94); box-shadow: 0 8px 24px rgba(16,35,61,.12); z-index: 2; cursor: pointer; font-size:0; color:#07194a; transition:background .16s ease, transform .16s ease, box-shadow .16s ease; }
        .doctor-nav::before { display:block; font-size:30px; line-height:1; font-weight:800; }
        .doctor-nav:hover { background:#fff; transform:translateY(-50%) scale(1.06); box-shadow:0 12px 28px rgba(16,35,61,.18); }
        .doctor-prev { left: 6px; }
        .doctor-prev::before { content:"‹"; }
        .doctor-next { right: 6px; }
        .doctor-next::before { content:"›"; }
        .doctor-dots { display: flex; justify-content: center; gap: 8px; }
        .doctor-dots button { width: 10px; height: 10px; border-radius: 999px; border: 0; background: #c9d5ea; }
        .doctor-dots button.is-active { background: var(--accent); }
        .physio-services-grid { display: grid; grid-template-columns: repeat(var(--physio-desktop-columns), minmax(0, 1fr)); gap: 14px; width: 100%; overflow: hidden; }
        .physio-service-card { border: 1px solid var(--line); border-radius: 14px; background: #fff; padding: 16px; display: grid; gap: 12px; min-width: 0; }
        .physio-service-card.is-featured { border-color: rgba(15,118,110,.32); box-shadow: 0 10px 24px rgba(15,118,110,.08); }
        .physio-service-card h3 { margin: 8px 0 6px; font-size: 18px; line-height: 1.25; overflow-wrap: anywhere; }
        .physio-icon { width: 38px; height: 38px; border-radius: 10px; display: grid; place-items: center; background: #ecf8f6; color: var(--accent); font-weight: 800; font-size: 22px; }

        @media (max-width: 1180px) {
            .why-layout { grid-template-columns: 1fr; }
        }

        @media (max-width: 960px) {
            .cards-2, .cards-3, .cards-4, .stats-grid, .highlight-strip, .why-layout, .facility-row { grid-template-columns: 1fr; }
            .test-category { flex-basis:100%; }
            .test-modal-body { grid-template-columns:1fr; }
            .physio-services-grid { grid-template-columns: repeat(var(--physio-tablet-columns), minmax(0, 1fr)); }
            .hero-slider { width:100%; height:auto; min-height: 0; }
            .hero-stage { height:auto; min-height: 0; }
            .hero-slider::before { background:linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.64)); }
            .hero-slide { position: relative; grid-template-columns: 1fr; opacity: 1; pointer-events: auto; transform: none; margin-bottom: 14px; width:100%; min-height:720px; }
            .hero-bg img { object-position: 62% center; transform:none; }
            .quick-panel { position:static; right:auto; bottom:auto; width:min(100% - 24px, 1180px); grid-template-columns:1fr 1fr; margin:0 auto 14px; border-radius:16px; background:rgba(255,255,255,.72); }
            .quick-panel a { min-height:82px; padding:14px; border-right:0; border-bottom:1px solid rgba(11,102,195,.14); }
            .hero-trust-row { grid-template-columns:1fr 1fr; }
            .hero-media { min-height: 300px; padding: 0 12px 0; order:-1; }
            .hero-copy { width:min(100% - 32px, 720px); padding: 42px 0 20px; }
            .hero-copy, .hero-media { border-radius: 12px; }
            .highlight-strip, .section-cta, .section-head { flex-direction: column; align-items: flex-start; }
            .appointment-cta-card .cta-actions { margin-left:0; width:100%; }
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
            .hero-copy .section-title { font-size: 30px; }
            .hero-copy .section-subtitle { font-size: 14px; }
            .hero-badge { font-size:12px; padding:7px 10px; }
            .hero-trust-row { grid-template-columns:1fr; }
            .hero-trust-item strong { font-size:13px; }
            .hero-trust-item span span { font-size:11px; }
            .quick-panel { grid-template-columns:1fr; }
            .quick-panel a { min-height:72px; padding:12px; gap:12px; }
            .quick-panel .icon-ring { width:42px; height:42px; }
            .quick-panel .icon-ring svg { width:26px; height:26px; }
            .quick-panel strong { font-size:14px; }
            .quick-panel span.quick-subtitle { font-size:12px; margin-top:3px; }
            .test-modal { padding:12px; }
            .test-modal-card { max-height:86vh; border-radius:12px; }
            .test-modal-head { padding:14px; }
            .test-modal-head h3 { font-size:22px; }
            .test-modal-body { padding:14px; gap:12px; }
            .home-sample-card { grid-template-columns:1fr; }
            .why-panel { padding:18px; }
            .why-stats-panel { padding:16px; }
            .why-stats-grid { gap:12px; }
            .why-stat { min-height:92px; padding:16px 14px; }
            .why-stat strong { font-size:26px; }
            .why-stat span { font-size:13px; }
            .building-tags { grid-template-columns:1fr 1fr; padding:12px; gap:8px; }
            .building-tags a { font-size:11px; }
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
            let animationFrame = null;
            let activeIndex = 0;
            let isDragging = false;
            let dragStartX = 0;
            let dragStartScroll = 0;

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

                if (animationFrame) {
                    window.cancelAnimationFrame(animationFrame);
                    animationFrame = null;
                }
            };

            const start = () => {
                if (! autoScroll || cards.length <= visibleCards()) {
                    return;
                }

                stop();
                const pixelsPerFrame = speed > 0 ? Math.max(.25, Math.min(1.6, 4500 / speed * .6)) : .6;

                const tick = () => {
                    if (! isDragging) {
                        const maxScroll = track.scrollWidth - viewport.clientWidth;

                        if (viewport.scrollLeft >= maxScroll - 1) {
                            if (loop) {
                                viewport.scrollLeft = 0;
                            } else {
                                stop();
                                return;
                            }
                        } else {
                            viewport.scrollLeft += pixelsPerFrame;
                        }

                        updateDots();
                    }

                    animationFrame = window.requestAnimationFrame(tick);
                };

                animationFrame = window.requestAnimationFrame(tick);
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

            viewport.addEventListener('pointerdown', (event) => {
                isDragging = true;
                dragStartX = event.clientX;
                dragStartScroll = viewport.scrollLeft;
                viewport.classList.add('is-dragging');
                viewport.setPointerCapture?.(event.pointerId);
                stop();
            });

            viewport.addEventListener('pointermove', (event) => {
                if (! isDragging) {
                    return;
                }

                viewport.scrollLeft = dragStartScroll - (event.clientX - dragStartX);
            });

            const endDrag = (event) => {
                if (! isDragging) {
                    return;
                }

                isDragging = false;
                viewport.classList.remove('is-dragging');
                viewport.releasePointerCapture?.(event.pointerId);
                start();
            };

            viewport.addEventListener('pointerup', endDrag);
            viewport.addEventListener('pointercancel', endDrag);
            viewport.addEventListener('pointerleave', endDrag);

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

        document.querySelectorAll('[data-doctor-call-button]').forEach((button) => {
            button.addEventListener('click', (event) => {
                const card = button.closest('.doctor-card-item');

                if (card && ! card.classList.contains('is-number-visible')) {
                    event.preventDefault();
                    card.classList.add('is-number-visible');
                }
            });
        });

        document.querySelectorAll('[data-quick-action]').forEach((link) => {
            link.addEventListener('pointerdown', (event) => {
                const rect = link.getBoundingClientRect();
                link.style.setProperty('--quick-x', `${event.clientX - rect.left}px`);
                link.style.setProperty('--quick-y', `${event.clientY - rect.top}px`);
                link.classList.remove('is-pressing');
                void link.offsetWidth;
                link.classList.add('is-pressing');
            });

            link.addEventListener('animationend', () => {
                link.classList.remove('is-pressing');
            });
        });

        document.querySelectorAll('[data-test-category-carousel]').forEach((carousel) => {
            let isDown = false;
            let startX = 0;
            let scrollLeft = 0;

            carousel.addEventListener('pointerdown', (event) => {
                isDown = true;
                startX = event.pageX;
                scrollLeft = carousel.scrollLeft;
                carousel.setPointerCapture?.(event.pointerId);
            });

            carousel.addEventListener('pointermove', (event) => {
                if (! isDown) {
                    return;
                }

                carousel.scrollLeft = scrollLeft - (event.pageX - startX);
            });

            ['pointerup', 'pointercancel', 'pointerleave'].forEach((eventName) => {
                carousel.addEventListener(eventName, () => {
                    isDown = false;
                });
            });
        });

        document.querySelectorAll('[data-test-modal]').forEach((modal) => {
            const section = modal.closest('.section-block');
            const openButton = section?.querySelector('[data-open-test-modal]');
            const closeButtons = modal.querySelectorAll('[data-close-test-modal]');

            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            };

            openButton?.addEventListener('click', () => {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                modal.querySelector('[data-close-test-modal]')?.focus();
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });
        });
    </script>
@endsection
