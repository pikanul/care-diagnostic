@php
    $locale = app()->getLocale();
    $isBn = $isBn ?? ($locale === 'bn');
    $preview = $preview ?? false;
    $key = $section->section_key;
    $data = is_array($section->section_data ?? null) ? $section->section_data : [];
    $title = $isBn ? ($section->title_bn ?: $section->section_label_bn) : ($section->title_en ?: $section->section_label_en);
    $subtitle = $isBn ? $section->subtitle_bn : $section->subtitle_en;
    $summary = $isBn ? $section->summary_bn : $section->summary_en;
    $content = $isBn ? $section->content_bn : $section->content_en;
    $items = $data['items'] ?? [];
    $groups = $data['groups'] ?? [];
    $cta = $data['cta'] ?? [];
    $points = $data['points'] ?? [];
    $stats = $data['stats'] ?? [];
    $settings = $data['settings'] ?? [];
    $doctors = collect($data['doctors'] ?? [])
        ->filter(function ($doctor) use ($preview) {
            if (! is_array($doctor)) {
                return false;
            }

            if (! empty($preview)) {
                return true;
            }

            return array_key_exists('is_active', $doctor) ? (bool) $doctor['is_active'] : true;
        })
        ->sortBy(fn ($doctor) => (int) ($doctor['display_order'] ?? 0))
        ->values()
        ->all();
    $physioFallbackServices = \App\Support\HomepageBuilder::defaultContent('physiotherapy-services')['services'] ?? [];
    $physioServices = collect($data['services'] ?? $physioFallbackServices)
        ->filter(function ($service) use ($preview) {
            if (! is_array($service)) {
                return false;
            }

            if (! empty($preview)) {
                return true;
            }

            return array_key_exists('is_active', $service) ? (bool) $service['is_active'] : true;
        })
        ->sortBy(fn ($service) => (int) ($service['display_order'] ?? 0))
        ->values()
        ->all();
    $slides = collect($data['slides'] ?? [])
        ->filter(function ($slide) use ($preview) {
            if (! is_array($slide)) {
                return false;
            }

            if (! empty($preview)) {
                return true;
            }

            $isActive = array_key_exists('is_active', $slide) ? (bool) $slide['is_active'] : true;
            if (! $isActive) {
                return false;
            }

            if (! blank($slide['publish_at'] ?? null) && \Illuminate\Support\Carbon::parse($slide['publish_at'])->isFuture()) {
                return false;
            }

            if (! blank($slide['expires_at'] ?? null) && \Illuminate\Support\Carbon::parse($slide['expires_at'])->isPast()) {
                return false;
            }

            return true;
        })
        ->sortBy(fn ($slide) => (int) ($slide['display_order'] ?? 0))
        ->values()
        ->all();

    $localizedUrl = function (string $url) use ($locale): string {
        if (preg_match('~^/(en|bn)(/|#|$)~', $url)) {
            return preg_replace('~^/(en|bn)(/|#|$)~', '/'.$locale.'$2', $url);
        }

        return $url;
    };

    $limit = $section->display_limit ?: null;
    $items = $limit ? array_slice($items, 0, $limit) : $items;
    $points = $limit ? array_slice($points, 0, $limit) : $points;
    $stats = $limit ? array_slice($stats, 0, $limit) : $stats;
    $doctors = $limit ? array_slice($doctors, 0, $limit) : $doctors;
    $physioServices = $limit ? array_slice($physioServices, 0, $limit) : $physioServices;
    $slides = $limit ? array_slice($slides, 0, $limit) : $slides;

    $sliderAutoPlay = (bool) ($settings['auto_play'] ?? $section->auto_scroll);
    $sliderPauseOnHover = (bool) ($settings['pause_on_hover'] ?? true);
    $sliderSwipe = (bool) ($settings['swipe'] ?? true);
    $sliderKeyboard = (bool) ($settings['keyboard'] ?? true);
    $sliderDots = (bool) ($settings['dots'] ?? true);
    $sliderArrows = (bool) ($settings['arrows'] ?? true);
    $sliderLazy = (bool) ($settings['lazy_load_after_first_slide'] ?? true);
    $sliderSpeed = (int) ($section->carousel_speed ?: 4500);
    $doctorAutoScroll = (bool) ($settings['auto_scroll'] ?? $section->auto_scroll);
    $doctorPauseOnHover = (bool) ($settings['pause_on_hover'] ?? true);
    $doctorShowArrows = (bool) ($settings['show_arrows'] ?? true);
    $doctorShowDots = (bool) ($settings['show_dots'] ?? false);
    $doctorLoop = (bool) ($settings['loop'] ?? true);
    $doctorDesktopCards = max(1, (int) ($settings['cards_per_view_desktop'] ?? 3));
    $doctorMobileCards = max(1, min(2, (int) ($settings['cards_per_view_mobile'] ?? 1)));
    $physioColumnsDesktop = max(1, (int) ($settings['columns_desktop'] ?? 4));
    $physioColumnsTablet = max(1, (int) ($settings['columns_tablet'] ?? 2));
    $physioColumnsMobile = max(1, min(2, (int) ($settings['columns_mobile'] ?? 1)));
    $physioShowIcons = (bool) ($settings['show_icons'] ?? true);
    $physioShowCta = (bool) ($settings['show_cta'] ?? true);
    $sectionByKey = fn (string $sectionKey) => \App\Models\HomepageSection::query()
        ->where('section_key', $sectionKey)
        ->where('is_active', true)
        ->first();
@endphp

@if (! in_array($key, ['top-information-bar', 'main-navigation', 'footer'], true))
    <section class="section-block" id="{{ $key }}" style="{{ $section->background_color ? 'background-color: '.$section->background_color.';' : '' }}{{ $section->background_image_path ? 'background-image:url('.asset('storage/'.$section->background_image_path).');background-size:cover;background-position:center;' : '' }}">
        <div class="section-inner">
            @switch($key)
                @case('hero-slider')
                    @php
                        $trustSection = $sectionByKey('trust-highlights');
                        $trustData = is_array($trustSection?->section_data) ? $trustSection->section_data : [];
                        $quickSection = $sectionByKey('quick-action-panel');
                        $quickData = is_array($quickSection?->section_data) ? $quickSection->section_data : [];
                    @endphp
                    <div class="hero-shell">
                        <div class="hero-slider"
                            data-hero-slider
                            tabindex="0"
                            data-auto-play="{{ $sliderAutoPlay ? '1' : '0' }}"
                            data-pause-on-hover="{{ $sliderPauseOnHover ? '1' : '0' }}"
                            data-swipe="{{ $sliderSwipe ? '1' : '0' }}"
                            data-keyboard="{{ $sliderKeyboard ? '1' : '0' }}"
                            data-dots="{{ $sliderDots ? '1' : '0' }}"
                            data-arrows="{{ $sliderArrows ? '1' : '0' }}"
                            data-lazy-load="{{ $sliderLazy ? '1' : '0' }}"
                            data-speed="{{ $sliderSpeed }}">
                            @if ($slides)
                                <div class="hero-stage">
                                    @foreach ($slides as $slideIndex => $slide)
                                        @php
                                            $alignment = $slide['text_alignment'] ?? $settings['text_alignment'] ?? 'left';
                                            $overlay = (int) ($slide['overlay_opacity'] ?? $settings['overlay_opacity'] ?? 60);
                                            $isLazy = $sliderLazy && $slideIndex > 0;
                                            $desktopImage = $slide['desktop_image_path'] ?? null;
                                            $mobileImage = $slide['mobile_image_path'] ?? null;
                                            $videoPath = $slide['video_path'] ?? null;
                                            $videoUrl = $slide['video_url'] ?? null;
                                            $videoPoster = $mobileImage ?: $desktopImage;
                                        @endphp
                                        <article class="hero-slide {{ $loop->first ? 'is-active' : '' }}" data-slide data-slide-index="{{ $slideIndex }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}" style="--hero-overlay: {{ $overlay / 100 }}; --hero-text-align: {{ $alignment }};">
                                            @if ($desktopImage)
                                                <div class="hero-bg" aria-hidden="true">
                                                    <picture>
                                                        @if ($mobileImage)
                                                            <source media="(max-width: 768px)" srcset="{{ $isLazy ? '' : asset('storage/'.$mobileImage) }}" @if ($isLazy) data-srcset="{{ asset('storage/'.$mobileImage) }}" @endif>
                                                        @endif
                                                        <img
                                                            alt=""
                                                            @if ($isLazy)
                                                                src="data:image/gif;base64,R0lGODlhAQABAAAAACw="
                                                                data-src="{{ asset('storage/'.$desktopImage) }}"
                                                            @else
                                                                src="{{ asset('storage/'.$desktopImage) }}"
                                                            @endif
                                                            loading="{{ $isLazy ? 'lazy' : 'eager' }}"
                                                            decoding="async"
                                                        >
                                                    </picture>
                                                </div>
                                            @endif

                                            @if (($slide['media_type'] ?? 'image') === 'video' || ! $desktopImage)
                                                <div class="hero-media">
                                                    @if (($slide['media_type'] ?? 'image') === 'video')
                                                    @php
                                                        $videoSource = $videoPath ? asset('storage/'.$videoPath) : $videoUrl;
                                                    @endphp
                                                    @if ($videoSource)
                                                        <video
                                                            controls
                                                            playsinline
                                                            preload="{{ $isLazy ? 'none' : 'metadata' }}"
                                                            poster="{{ $videoPoster ? asset('storage/'.$videoPoster) : '' }}"
                                                            @if ($isLazy) data-src="{{ $videoSource }}" @else src="{{ $videoSource }}" @endif
                                                        ></video>
                                                    @else
                                                        <div class="hero-placeholder" aria-label="Hero video placeholder"></div>
                                                    @endif
                                                    @else
                                                        <div class="hero-placeholder" aria-label="Hero image placeholder"></div>
                                                    @endif
                                                </div>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>

                                @if (count($slides) > 1)
                                    @if ($sliderArrows)
                                        <div class="slide-nav">
                                            <button type="button" data-prev aria-label="Previous slide">Prev</button>
                                            <button type="button" data-next aria-label="Next slide">Next</button>
                                        </div>
                                    @endif
                                    @if ($sliderDots)
                                        <div class="hero-indicators" data-indicators>
                                            @foreach ($slides as $slide)
                                                <button type="button" data-indicator aria-label="Slide {{ $loop->iteration }}" @if ($loop->first) aria-current="true" class="is-active" @endif></button>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            @else
                                <div class="hero-stage">
                                    <div class="hero-slide is-active">
                                        <div class="hero-copy">
                                            <h3 class="section-title" style="color:#fff;">{{ $title }}</h3>
                                            <p class="section-subtitle">No hero slides are active yet.</p>
                                        </div>
                                        <div class="hero-media">
                                            <div class="hero-placeholder" aria-label="Hero image placeholder"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (! empty($quickData['actions']))
                                <div class="quick-panel">
                                    @foreach (array_slice($quickData['actions'], 0, 4) as $action)
                                        @php
                                            $quickTitle = $isBn ? ($action['title_bn'] ?? $action['title_en'] ?? '') : ($action['title_en'] ?? $action['title_bn'] ?? '');
                                        @endphp
                                        <a href="{{ $localizedUrl($action['url'] ?? '#') }}" data-quick-action aria-label="{{ $quickTitle }}">
                                            <span class="icon-ring" aria-hidden="true">
                                                <svg viewBox="0 0 64 64">
                                                    @switch($loop->index)
                                                        @case(0)
                                                            <path d="M32 9a23 23 0 1 0 20 12" />
                                                            <path d="M50 10v12H38" />
                                                            <path d="M25 26v12" />
                                                            <path d="M39 26v12" />
                                                            <path d="M22 32h20" />
                                                            @break
                                                        @case(1)
                                                            <path d="M13 29 32 13l19 16" />
                                                            <path d="M18 27v26h28V27" />
                                                            <path d="M27 53V38h10v15" />
                                                            @break
                                                        @case(2)
                                                            <path d="M17 14h30v38H17z" />
                                                            <path d="M24 8v12M40 8v12" />
                                                            <path d="M24 35l6 6 11-13" />
                                                            @break
                                                        @default
                                                            <path d="M32 9 49 17v14c0 11-7 18-17 23-10-5-17-12-17-23V17l17-8Z" />
                                                            <path d="M22 44c2-6 7-10 10-10s8 4 10 10" />
                                                            <circle cx="32" cy="26" r="7" />
                                                    @endswitch
                                                </svg>
                                            </span>
                                            <span>
                                                <strong>{{ $quickTitle }}</strong>
                                                @if (! empty($action['subtitle_en']) || ! empty($action['subtitle_bn']))
                                                    <span class="quick-subtitle">{{ $isBn ? ($action['subtitle_bn'] ?? $action['subtitle_en'] ?? '') : ($action['subtitle_en'] ?? $action['subtitle_bn'] ?? '') }}</span>
                                                @endif
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    @break

                @case('main-services')
                    <div class="section-head">
                        <div style="width:100%;text-align:center;">
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $title }}</h2>
                            @if ($subtitle)
                                <p class="section-subtitle">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="section-grid cards-3">
                        @foreach ($items as $item)
                            @php
                                $cardImage = $item['image_path'] ?? $item['desktop_image_path'] ?? null;
                                $cardAccent = $item['accent'] ?? '#0f766e';
                            @endphp
                            <article class="card-item" style="--card-accent: {{ $cardAccent }};">
                                @if ($cardImage)
                                    <img class="card-image" src="{{ asset('storage/'.$cardImage) }}" alt="{{ $isBn ? ($item['title_bn'] ?? $item['title_en'] ?? '') : ($item['title_en'] ?? $item['title_bn'] ?? '') }}" loading="lazy" decoding="async">
                                    <div class="service-dot" aria-hidden="true">+</div>
                                @endif
                                <h3 style="margin:10px 0 8px;">{{ $isBn ? ($item['title_bn'] ?? $item['title_en'] ?? '') : ($item['title_en'] ?? $item['title_bn'] ?? '') }}</h3>
                                @if (! empty($item['subtitle_en']))
                                    <div class="muted-text">{{ $isBn ? ($item['subtitle_bn'] ?? $item['subtitle_en']) : $item['subtitle_en'] }}</div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                    @break

                @case('facility-showcase')
                    <div class="section-head">
                        <div style="width:100%;text-align:center;">
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $title }}</h2>
                        </div>
                    </div>
                    <div class="facility-row">
                        @foreach ($items as $item)
                            <article class="card-item">
                                <div class="icon-ring" style="margin:0 auto 8px;" aria-hidden="true">+</div>
                                <h3 style="margin:0;">{{ $isBn ? ($item['title_bn'] ?? $item['title_en'] ?? '') : ($item['title_en'] ?? $item['title_bn'] ?? '') }}</h3>
                            </article>
                        @endforeach
                    </div>
                    @break

                @case('trust-highlights')
                @case('health-packages')
                @case('testimonials')
                @case('latest-articles')
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $title }}</h2>
                            @if ($subtitle)
                                <p class="section-subtitle">{{ $subtitle }}</p>
                            @endif
                            @if ($summary)
                                <p class="section-subtitle">{{ $summary }}</p>
                            @endif
                        </div>
                    </div>
                    @if (! empty($data['image_path']))
                        <img class="section-visual" src="{{ asset('storage/'.$data['image_path']) }}" alt="{{ $title }}" loading="lazy" decoding="async" style="margin-bottom:14px;">
                    @endif
                    <div class="section-grid cards-3">
                        @foreach ($items as $item)
                            @php
                                $cardImage = $item['image_path'] ?? $item['desktop_image_path'] ?? null;
                                $cardAccent = $item['accent'] ?? '#0f766e';
                            @endphp
                            <article class="card-item" style="--card-accent: {{ $cardAccent }};">
                                @if ($cardImage)
                                    <img class="card-image" src="{{ asset('storage/'.$cardImage) }}" alt="{{ $isBn ? ($item['title_bn'] ?? $item['title_en'] ?? '') : ($item['title_en'] ?? $item['title_bn'] ?? '') }}" loading="lazy" decoding="async">
                                    <div class="service-dot" aria-hidden="true">+</div>
                                @endif
                                <h3 style="margin:10px 0 8px;">{{ $isBn ? ($item['title_bn'] ?? $item['title_en'] ?? '') : ($item['title_en'] ?? $item['title_bn'] ?? '') }}</h3>
                                @if (! empty($item['subtitle_en']))
                                    <div class="muted-text">{{ $isBn ? ($item['subtitle_bn'] ?? $item['subtitle_en']) : $item['subtitle_en'] }}</div>
                                @endif
                                @if (! empty($item['description_en']))
                                    <p class="muted-text">{{ $isBn ? ($item['description_bn'] ?? $item['description_en']) : $item['description_en'] }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                    @break

                @case('diagnostic-test-categories')
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $isBn ? ($cta['title_bn'] ?? $title) : ($cta['title_en'] ?? $title) }}</h2>
                            @if ($subtitle)
                                <p class="section-subtitle">{{ $subtitle }}</p>
                            @endif
                        </div>
                        @if (! empty($cta['button_label_en']))
                            <button class="action-link" type="button" data-open-test-modal>
                                {{ $isBn ? ($cta['button_label_bn'] ?? $cta['button_label_en']) : $cta['button_label_en'] }}
                            </button>
                        @endif
                    </div>
                    <div class="test-category-grid" data-test-category-carousel aria-label="{{ $title }}">
                        @foreach ($groups as $group)
                            @php
                                $tests = $isBn ? ($group['tests_bn'] ?? $group['tests_en'] ?? []) : ($group['tests_en'] ?? $group['tests_bn'] ?? []);
                            @endphp
                            <article class="test-category">
                                <h3>{{ $isBn ? ($group['title_bn'] ?? $group['title_en'] ?? '') : ($group['title_en'] ?? $group['title_bn'] ?? '') }}</h3>
                                <ul>
                                    @foreach (array_slice($tests, 0, 5) as $test)
                                        <li>{{ $test }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        @endforeach
                    </div>
                    <div class="test-modal" data-test-modal aria-hidden="true">
                        <div class="test-modal-backdrop" data-close-test-modal></div>
                        <div class="test-modal-card" role="dialog" aria-modal="true" aria-label="{{ $isBn ? 'সব পরীক্ষা' : 'All Tests' }}">
                            <div class="test-modal-head">
                                <div>
                                    <div class="section-kicker">{{ $section->section_label_en }}</div>
                                    <h3>{{ $isBn ? 'সব পরীক্ষা' : 'All Tests' }}</h3>
                                </div>
                                <button type="button" data-close-test-modal aria-label="{{ $isBn ? 'বন্ধ করুন' : 'Close' }}">×</button>
                            </div>
                            <div class="test-modal-body">
                                @foreach ($groups as $group)
                                    @php
                                        $tests = $isBn ? ($group['tests_bn'] ?? $group['tests_en'] ?? []) : ($group['tests_en'] ?? $group['tests_bn'] ?? []);
                                    @endphp
                                    <article class="test-modal-category">
                                        <h4>{{ $isBn ? ($group['title_bn'] ?? $group['title_en'] ?? '') : ($group['title_en'] ?? $group['title_bn'] ?? '') }}</h4>
                                        <ul>
                                            @foreach ($tests as $test)
                                                <li>{{ $test }}</li>
                                            @endforeach
                                        </ul>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @break

                @case('physiotherapy-services')
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $title }}</h2>
                            @if ($subtitle)
                                <p class="section-subtitle">{{ $subtitle }}</p>
                            @endif
                            @if ($summary)
                                <p class="section-subtitle">{{ $summary }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="physio-services-grid" style="--physio-desktop-columns: {{ $physioColumnsDesktop }}; --physio-tablet-columns: {{ $physioColumnsTablet }}; --physio-mobile-columns: {{ $physioColumnsMobile }};">
                        @foreach ($physioServices as $service)
                            @php
                                $serviceTitle = $isBn ? ($service['title_bn'] ?? $service['title_en'] ?? '') : ($service['title_en'] ?? $service['title_bn'] ?? '');
                                $serviceDescription = $isBn ? ($service['description_bn'] ?? $service['description_en'] ?? '') : ($service['description_en'] ?? $service['description_bn'] ?? '');
                                $ctaLabel = $isBn ? ($service['cta_label_bn'] ?? $service['cta_label_en'] ?? '') : ($service['cta_label_en'] ?? $service['cta_label_bn'] ?? '');
                                $ctaUrl = $service['cta_url'] ?? '';
                            @endphp
                            <article class="physio-service-card {{ ! empty($service['featured']) ? 'is-featured' : '' }}">
                                @if ($physioShowIcons)
                                    <div class="physio-icon" aria-hidden="true">+</div>
                                @endif
                                <div>
                                    @if (! empty($service['featured']))
                                        <span class="doctor-badge">{{ $isBn ? 'ফিচার্ড' : 'Featured' }}</span>
                                    @endif
                                    <h3>{{ $serviceTitle }}</h3>
                                    @if ($serviceDescription)
                                        <p class="muted-text">{{ $serviceDescription }}</p>
                                    @endif
                                </div>
                                @if ($physioShowCta && $ctaLabel && $ctaUrl)
                                    <a class="action-link" href="{{ $localizedUrl($ctaUrl) }}">{{ $ctaLabel }}</a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                    @break

                @case('specialist-doctors')
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $title }}</h2>
                            @if ($subtitle)
                                <p class="section-subtitle">{{ $subtitle }}</p>
                            @endif
                            @if ($summary)
                                <p class="section-subtitle">{{ $summary }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="doctor-carousel" data-doctor-carousel
                        data-auto-scroll="{{ $doctorAutoScroll ? '1' : '0' }}"
                        data-pause-on-hover="{{ $doctorPauseOnHover ? '1' : '0' }}"
                        data-show-arrows="{{ $doctorShowArrows ? '1' : '0' }}"
                        data-show-dots="{{ $doctorShowDots ? '1' : '0' }}"
                        data-loop="{{ $doctorLoop ? '1' : '0' }}"
                        data-speed="{{ $sliderSpeed }}"
                        style="--doctor-desktop-cards: {{ count($doctors) > 5 ? min($doctorDesktopCards, 5) : $doctorDesktopCards }}; --doctor-mobile-cards: {{ $doctorMobileCards }};">
                        @if ($doctorShowArrows && count($doctors) > 1)
                            <button type="button" class="doctor-nav doctor-prev" data-doctor-prev aria-label="Previous doctors">Prev</button>
                            <button type="button" class="doctor-nav doctor-next" data-doctor-next aria-label="Next doctors">Next</button>
                        @endif
                        <div class="doctor-viewport">
                            <div class="doctor-track" data-doctor-track>
                                @foreach ($doctors as $doctor)
                                    @php
                                        $doctorName = $isBn ? ($doctor['name_bn'] ?? $doctor['name_en'] ?? '') : ($doctor['name_en'] ?? $doctor['name_bn'] ?? '');
                                        $doctorSchedule = $isBn ? ($doctor['schedule_bn'] ?? $doctor['schedule_en'] ?? '') : ($doctor['schedule_en'] ?? $doctor['schedule_bn'] ?? '');
                                        $doctorPhoto = $doctor['desktop_image_path'] ?? null;
                                        $doctorMobilePhoto = $doctor['mobile_image_path'] ?? null;
                                        $profileUrl = $doctor['profile_url'] ?? '';
                                        $appointmentUrl = $doctor['appointment_url'] ?? '';
                                        $callUrl = $doctor['call_url'] ?? '';
                                        $callDisplay = preg_replace('/^tel:/', '', $callUrl);
                                        $degrees = $doctor['degrees'] ?? '';
                                        $specialty = $doctor['specialty'] ?? '';
                                        $department = $doctor['department'] ?? '';
                                    @endphp
                                    <article class="doctor-card-item">
                                        <div class="doctor-photo">
                                            @if ($doctorPhoto)
                                                <picture>
                                                    @if ($doctorMobilePhoto)
                                                        <source media="(max-width: 768px)" srcset="{{ asset('storage/'.$doctorMobilePhoto) }}">
                                                    @endif
                                                    <img src="{{ asset('storage/'.$doctorPhoto) }}" alt="{{ $doctorName }}" loading="lazy" decoding="async">
                                                </picture>
                                            @else
                                                <div class="doctor-placeholder" aria-label="Doctor photo placeholder"></div>
                                            @endif
                                            @if (! empty($doctor['featured']))
                                                <span class="doctor-badge">Featured</span>
                                            @endif
                                        </div>
                                        <div class="doctor-body">
                                            <h3>{{ $doctorName }}</h3>
                                            @if ($degrees)
                                                <p class="doctor-meta">{{ $degrees }}</p>
                                            @endif
                                            @if ($specialty)
                                                <p class="doctor-meta"><strong>{{ $isBn ? 'বিশেষত্ব' : 'Specialty' }}:</strong> {{ $specialty }}</p>
                                            @endif
                                            @if ($department)
                                                <p class="doctor-meta"><strong>{{ $isBn ? 'বিভাগ' : 'Department' }}:</strong> {{ $department }}</p>
                                            @endif
                                            @if ($doctorSchedule)
                                                <p class="doctor-meta"><strong>{{ $isBn ? 'সময়সূচি' : 'Schedule' }}:</strong> {{ $doctorSchedule }}</p>
                                            @endif
                                            <div class="doctor-actions">
                                                @if ($profileUrl)
                                                    <a class="action-link" href="{{ $localizedUrl($profileUrl) }}">{{ $isBn ? 'প্রোফাইল দেখুন' : 'View profile' }}</a>
                                                @endif
                                                @if (! empty($doctor['call_enabled']) && $callUrl)
                                                    <a class="action-link doctor-book-call" href="{{ $callUrl }}" data-doctor-call-button data-call-number="{{ $callDisplay }}">
                                                        {{ $isBn ? 'কল করে অ্যাপয়েন্টমেন্ট' : 'Call to book appointment' }}
                                                    </a>
                                                    <span class="doctor-call-number" data-doctor-call-number>{{ $callDisplay }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                        @if ($doctorShowDots && count($doctors) > 1)
                            <div class="doctor-dots" data-doctor-dots>
                                @foreach ($doctors as $doctor)
                                    <button type="button" data-doctor-dot aria-label="Doctor {{ $loop->iteration }}" @if ($loop->first) class="is-active" aria-current="true" @endif></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @break

                @case('quick-action-panel')
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $title }}</h2>
                        </div>
                    </div>
                    <div class="section-grid cards-4">
                        @foreach (($data['actions'] ?? []) as $action)
                            <a class="action-item" href="{{ $localizedUrl($action['url'] ?? '#') }}">
                                <strong>{{ $isBn ? ($action['title_bn'] ?? $action['title_en']) : $action['title_en'] }}</strong>
                            </a>
                        @endforeach
                    </div>
                    @break

                @case('quality-highlight-strip')
                    <div class="highlight-strip">
                        @foreach (($data['items'] ?? []) as $item)
                            <div class="highlight-item">
                                <strong>{{ $isBn ? ($item['title_bn'] ?? $item['title_en'] ?? '') : ($item['title_en'] ?? $item['title_bn'] ?? '') }}</strong>
                                <span>{{ $isBn ? ($item['subtitle_bn'] ?? $item['subtitle_en'] ?? '') : ($item['subtitle_en'] ?? $item['subtitle_bn'] ?? '') }}</span>
                            </div>
                        @endforeach
                    </div>
                    @break

                @case('why-choose-us')
                    @php
                        $facilitySection = $sectionByKey('facility-showcase');
                        $facilityData = is_array($facilitySection?->section_data) ? $facilitySection->section_data : [];
                    @endphp
                    <div class="why-layout">
                        <div class="why-panel">
                            <div class="section-kicker">{{ $isBn ? $section->section_label_bn : $section->section_label_en }}</div>
                            <h3>
                                @if (! $isBn && $title === 'Your Health Is Our Commitment')
                                    Your Health Is<br>Our Commitment
                                @else
                                    {{ $title }}
                                @endif
                            </h3>
                            <p class="muted-text">{{ $summary ?: $content ?: $subtitle }}</p>
                            <ul>
                                @foreach ($points as $point)
                                    <li>{{ $isBn ? ($point['title_bn'] ?? $point['title_en']) : $point['title_en'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="why-stats-panel">
                            <div class="why-stats-grid">
                                @foreach (array_slice(($data['stats'] ?? []), 0, 4) as $stat)
                                    <div class="why-stat">
                                        <strong>{{ $stat['value'] ?? '' }}</strong>
                                        <span>{{ $isBn ? ($stat['title_bn'] ?? $stat['title_en'] ?? '') : ($stat['title_en'] ?? $stat['title_bn'] ?? '') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="why-building">
                            @if (! empty($facilityData['image_path']))
                                <img src="{{ asset('storage/'.$facilityData['image_path']) }}" alt="{{ $title }}" loading="lazy" decoding="async">
                            @endif
                            <div class="building-tags">
                                @foreach (array_slice($facilityData['items'] ?? [], 0, 4) as $facilityItem)
                                    <a href="{{ $localizedUrl($facilityItem['url'] ?? '/en#facility-showcase') }}">
                                        {{ $isBn ? ($facilityItem['title_bn'] ?? $facilityItem['title_en'] ?? '') : ($facilityItem['title_en'] ?? $facilityItem['title_bn'] ?? '') }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @break

                @case('statistics')
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $title }}</h2>
                        </div>
                    </div>
                    <div class="section-grid stats-grid">
                        @foreach ($stats as $stat)
                            <article class="stats-item">
                                <div class="stat-value">{{ $stat['value'] ?? '00+' }}</div>
                                <div class="muted-text">{{ $isBn ? ($stat['title_bn'] ?? $stat['title_en'] ?? '') : ($stat['title_en'] ?? $stat['title_bn'] ?? '') }}</div>
                            </article>
                        @endforeach
                    </div>
                    @break

                @case('home-sample-collection')
                @case('appointment-cta')
                    @php
                        $primaryLabel = $isBn ? ($data['primary_cta_label_bn'] ?? $data['button_label_bn'] ?? '') : ($data['primary_cta_label_en'] ?? $data['button_label_en'] ?? '');
                        $primaryUrl = $data['primary_cta_url'] ?? $data['button_url'] ?? ($section->section_key === 'home-sample-collection' ? '/en#home-sample-collection' : '/en#appointment-cta');
                        $secondaryLabel = $isBn ? ($data['secondary_cta_label_bn'] ?? '') : ($data['secondary_cta_label_en'] ?? '');
                        $secondaryUrl = $data['secondary_cta_url'] ?? '';
                        $ctaPoints = $data['points'] ?? [];
                        $ctaImage = $data['image_path'] ?? null;
                    @endphp
                    <div @class(['section-cta', 'appointment-cta-card' => $section->section_key === 'appointment-cta'])>
                        <div>
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $title }}</h2>
                            <p class="section-subtitle">{{ $content ?: $summary }}</p>
                            @if ($ctaPoints)
                                <ul style="margin:12px 0 0;padding-left:18px;line-height:1.8;">
                                    @foreach ($ctaPoints as $point)
                                        <li>{{ $isBn ? ($point['title_bn'] ?? $point['title_en'] ?? '') : ($point['title_en'] ?? $point['title_bn'] ?? '') }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="cta-actions" style="display:flex;gap:10px;flex-wrap:wrap;">
                            @if ($secondaryLabel && $secondaryUrl)
                                <a class="action-link" href="{{ $localizedUrl($secondaryUrl) }}">{{ $secondaryLabel }}</a>
                            @endif
                            @if ($section->section_key !== 'appointment-cta' && $primaryLabel && $primaryUrl)
                                <a class="action-link primary" href="{{ $localizedUrl($primaryUrl) }}">{{ $primaryLabel }}</a>
                            @endif
                        </div>
                        @if ($ctaImage)
                            <div class="cta-image">
                                <img src="{{ asset('storage/'.$ctaImage) }}" alt="{{ $title }}" loading="lazy" decoding="async">
                            </div>
                        @endif
                    </div>
                    @break

                @default
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">{{ $section->section_label_en }}</div>
                            <h2 class="section-title">{{ $title }}</h2>
                            @if ($subtitle)
                                <p class="section-subtitle">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>
                    <p class="muted-text">Section ready for admin content.</p>
            @endswitch
        </div>
    </section>
@endif
