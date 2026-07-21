@extends('layouts.admin', ['title' => 'Edit Homepage Section'])

@php
    $data = $section->section_data ?? [];
    $jsonValue = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $doctorRefs = json_encode($section->related_doctor_refs ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $serviceRefs = json_encode($section->related_service_refs ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $testRefs = json_encode($section->related_test_refs ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $postRefs = json_encode($section->related_post_refs ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $heroData = old('hero_settings', $data['settings'] ?? []);
    $heroSlides = old('hero_slides', $data['slides'] ?? []);
    $doctorData = old('doctor_settings', $data['settings'] ?? []);
    $doctorCards = old('doctor_cards', $data['doctors'] ?? []);
    $physioData = old('physio_settings', $data['settings'] ?? []);
    $physioServices = old('physio_services', $data['services'] ?? []);

    if ($section->section_key === 'hero-slider' && empty($heroSlides)) {
        $heroSlides = [[]];
    }

    if ($section->section_key === 'specialist-doctors' && empty($doctorCards)) {
        $doctorCards = [[]];
    }

    if ($section->section_key === 'physiotherapy-services' && empty($physioServices)) {
        $physioServices = [[]];
    }

    $iniBytes = static function (string $value): int {
        $value = trim($value);
        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return match ($unit) {
            'g' => (int) ($number * 1024 * 1024 * 1024),
            'm' => (int) ($number * 1024 * 1024),
            'k' => (int) ($number * 1024),
            default => (int) $number,
        };
    };

    $phpUploadBytes = $iniBytes((string) ini_get('upload_max_filesize'));
    $phpPostBytes = $iniBytes((string) ini_get('post_max_size'));
    $runtimeUploadLimitBytes = min($phpUploadBytes, $phpPostBytes);
    $runtimeUploadLimitMb = max(1, floor($runtimeUploadLimitBytes / 1024 / 1024));
@endphp

@section('content')
    <style>
        .builder-grid {
            display: grid;
            width: 100%;
            gap: 20px;
            grid-template-columns: minmax(620px, 1.35fr) minmax(420px, .85fr);
            align-items: start;
        }

        .builder-grid > .panel {
            min-width: 0;
        }

        .builder-grid-single {
            grid-template-columns: 1fr;
        }

        .builder-panel {
            display: grid;
            gap: 16px;
        }

        .builder-panel > h2 {
            margin: 0;
            font-size: 24px;
            line-height: 1.2;
        }

        .builder-panel > .muted {
            margin-top: -8px;
        }

        .builder-preview {
            position: sticky;
            top: 24px;
            max-height: calc(100vh - 48px);
            overflow: auto;
        }

        .builder-preview .section-block {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
        }

        .builder-preview .section-inner {
            padding: 0;
        }

        .builder-preview .hero-slider {
            position: relative;
            height: 420px;
            overflow: hidden;
            border-radius: 12px;
            background: #eaf4ff;
        }

        .builder-preview .hero-stage,
        .builder-preview .hero-slide {
            position: relative;
            height: 100%;
        }

        .builder-preview .hero-slide:not(.is-active) {
            display: none;
        }

        .builder-preview .hero-bg {
            position: absolute;
            inset: 0;
        }

        .builder-preview .hero-bg picture,
        .builder-preview .hero-bg img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .builder-preview .hero-bg img {
            object-fit: cover;
            object-position: center;
        }

        .builder-preview .hero-copy {
            position: relative;
            z-index: 1;
            width: min(92%, 520px);
            padding: 34px;
            color: #07194a;
        }

        .builder-preview .hero-badge {
            display: inline-flex;
            width: fit-content;
            margin-bottom: 12px;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.86);
            color: #0b66c3;
            font-size: 12px;
            font-weight: 800;
        }

        .builder-preview .section-title {
            margin: 0;
            font-size: clamp(26px, 3vw, 38px);
            line-height: 1.1;
        }

        .builder-preview .section-subtitle {
            margin: 10px 0 0;
            color: #365477;
            line-height: 1.5;
        }

        .builder-preview .hero-actions,
        .builder-preview .hero-trust-row,
        .builder-preview .quick-panel,
        .builder-preview .hero-indicators,
        .builder-preview .slide-nav {
            display: none;
        }

        .subpanel {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 18px;
            background: #fbfdff;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
        }

        .subpanel h3 {
            margin: 0 0 12px;
            font-size: 17px;
            color: var(--text);
        }
        .hero-settings-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .hero-settings-grid label, .slide-grid label { display: grid; gap: 6px; }
        .hero-settings-grid input[type="checkbox"], .slide-grid input[type="checkbox"] { width: 16px; height: 16px; }
        .slide-builder { display: grid; gap: 12px; }
        .slide-card { border: 1px solid var(--line); border-radius: 12px; padding: 16px; background: #fff; display: grid; gap: 14px; box-shadow: 0 8px 22px rgba(16,35,61,.04); }
        .slide-card header { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .slide-grid { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .slide-grid .full { grid-column: 1 / -1; }
        .slide-preview { display: grid; gap: 8px; }
        .slide-preview img, .slide-preview video { width: 100%; max-height: 180px; object-fit: cover; border-radius: 10px; background: #f4f6f8; }
        .hero-add { width: fit-content; }
        .muted-hint { color: var(--muted); font-size: 12px; line-height: 1.5; }
        .upload-limit-warning {
            grid-column: 1 / -1;
            border: 1px solid #f3c56a;
            border-radius: 10px;
            padding: 12px 14px;
            color: #613d00;
            background: #fff8e6;
            font-size: 13px;
            line-height: 1.55;
        }
        .slide-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .danger-link { color: #a62323; }
        .doctor-builder { display: grid; gap: 14px; }
        .doctor-settings-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .doctor-settings-grid label, .doctor-card-grid label { display: grid; gap: 6px; }
        .doctor-card { border: 1px solid var(--line); border-radius: 12px; padding: 14px; background: #fff; display: grid; gap: 12px; }
        .doctor-card header { display: flex; justify-content: space-between; gap: 12px; align-items: center; }
        .doctor-card-grid { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .doctor-card-grid .full { grid-column: 1 / -1; }
        .doctor-preview { display: grid; gap: 8px; }
        .doctor-preview img { width: 100%; max-height: 180px; object-fit: cover; border-radius: 10px; background: #f4f6f8; }
        .doctor-add { width: fit-content; }
        .physio-builder { display: grid; gap: 14px; }
        .physio-settings-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .physio-settings-grid label, .physio-card-grid label { display: grid; gap: 6px; }
        .physio-settings-grid .full { grid-column: 1 / -1; }
        .physio-card { border: 1px solid var(--line); border-radius: 12px; padding: 14px; background: #fff; display: grid; gap: 12px; }
        .physio-card header { display: flex; justify-content: space-between; gap: 12px; align-items: center; }
        .physio-card-grid { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .physio-card-grid .full { grid-column: 1 / -1; }
        .physio-add { width: fit-content; }

        @media (max-width: 1300px) {
            .builder-grid { grid-template-columns: 1fr; }
            .builder-preview { position: static; max-height: none; }
        }

        @media (max-width: 720px) {
            .hero-settings-grid, .slide-grid, .doctor-settings-grid, .doctor-card-grid, .physio-settings-grid, .physio-card-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="builder-grid {{ $section->section_key === 'specialist-doctors' ? 'builder-grid-single' : '' }}">
        @if ($section->section_key === 'physiotherapy-services')
            <form class="panel builder-panel" method="POST" action="{{ route('admin.homepage.update', $section) }}" data-physio-form>
                @csrf
                @method('PUT')

                <h2 style="margin-top:0;">Physiotherapy and Rehabilitation Builder</h2>
                <p class="muted">Only active services appear publicly. All content is editable in Bangla and English.</p>
                <input type="hidden" name="section_type" value="cards">

                <div class="subpanel">
                    <h3>Section settings</h3>
                    <div class="physio-settings-grid">
                        <label>Section Label (English)
                            <input type="text" name="section_label_en" value="{{ old('section_label_en', $section->section_label_en) }}" required>
                        </label>
                        <label>Section Label (Bangla)
                            <input type="text" name="section_label_bn" value="{{ old('section_label_bn', $section->section_label_bn) }}" required>
                        </label>
                        <label>Display Order
                            <input type="number" name="display_order" value="{{ old('display_order', $section->display_order) }}" min="0" required>
                        </label>
                        <label>Display Limit
                            <input type="number" name="display_limit" value="{{ old('display_limit', $section->display_limit) }}" min="1">
                        </label>
                        <label>Background Color
                            <input type="text" name="background_color" value="{{ old('background_color', $section->background_color) }}" placeholder="#f5f7fb">
                        </label>
                        <label>Desktop Columns
                            <input type="number" name="physio_settings[columns_desktop]" value="{{ old('physio_settings.columns_desktop', $physioData['columns_desktop'] ?? 4) }}" min="1" max="6">
                        </label>
                        <label>Tablet Columns
                            <input type="number" name="physio_settings[columns_tablet]" value="{{ old('physio_settings.columns_tablet', $physioData['columns_tablet'] ?? 2) }}" min="1" max="4">
                        </label>
                        <label>Mobile Columns
                            <input type="number" name="physio_settings[columns_mobile]" value="{{ old('physio_settings.columns_mobile', $physioData['columns_mobile'] ?? 1) }}" min="1" max="2">
                        </label>
                        <label class="full">Title (English)
                            <input type="text" name="title_en" value="{{ old('title_en', $section->title_en) }}">
                        </label>
                        <label class="full">Title (Bangla)
                            <input type="text" name="title_bn" value="{{ old('title_bn', $section->title_bn) }}">
                        </label>
                        <label class="full">Subtitle (English)
                            <input type="text" name="subtitle_en" value="{{ old('subtitle_en', $section->subtitle_en) }}">
                        </label>
                        <label class="full">Subtitle (Bangla)
                            <input type="text" name="subtitle_bn" value="{{ old('subtitle_bn', $section->subtitle_bn) }}">
                        </label>
                        <label class="full">Summary (English)
                            <textarea name="summary_en" rows="3">{{ old('summary_en', $section->summary_en) }}</textarea>
                        </label>
                        <label class="full">Summary (Bangla)
                            <textarea name="summary_bn" rows="3">{{ old('summary_bn', $section->summary_bn) }}</textarea>
                        </label>
                        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Active</label>
                        <label class="check"><input type="checkbox" name="preview_enabled" value="1" @checked(old('preview_enabled', $section->preview_enabled))> Preview before publishing</label>
                        <label class="check"><input type="checkbox" name="physio_settings[show_icons]" value="1" @checked(old('physio_settings.show_icons', $physioData['show_icons'] ?? true))> Show icons</label>
                        <label class="check"><input type="checkbox" name="physio_settings[show_cta]" value="1" @checked(old('physio_settings.show_cta', $physioData['show_cta'] ?? true))> Show service CTA</label>
                    </div>
                </div>

                <div class="subpanel">
                    <h3>Services</h3>
                    <div class="physio-builder" data-physio-builder>
                        @foreach ($physioServices as $index => $service)
                            @php
                                $service = is_array($service) ? $service : [];
                            @endphp
                            <article class="physio-card" data-physio-card>
                                <header>
                                    <strong>Service {{ $loop->iteration }}</strong>
                                    <button type="button" class="danger-link" data-remove-physio>Remove</button>
                                </header>
                                <div class="physio-card-grid">
                                    <label>Display Order
                                        <input type="number" name="physio_services[{{ $index }}][display_order]" value="{{ old("physio_services.$index.display_order", $service['display_order'] ?? $loop->iteration) }}" min="0">
                                    </label>
                                    <label class="check"><input type="checkbox" name="physio_services[{{ $index }}][is_active]" value="1" @checked(old("physio_services.$index.is_active", $service['is_active'] ?? true))> Active</label>
                                    <label class="check"><input type="checkbox" name="physio_services[{{ $index }}][featured]" value="1" @checked(old("physio_services.$index.featured", $service['featured'] ?? false))> Featured</label>
                                    <label class="full">Title (English)
                                        <input type="text" name="physio_services[{{ $index }}][title_en]" value="{{ old("physio_services.$index.title_en", $service['title_en'] ?? '') }}" required>
                                    </label>
                                    <label class="full">Title (Bangla)
                                        <input type="text" name="physio_services[{{ $index }}][title_bn]" value="{{ old("physio_services.$index.title_bn", $service['title_bn'] ?? '') }}" required>
                                    </label>
                                    <label class="full">Description (English)
                                        <textarea name="physio_services[{{ $index }}][description_en]" rows="3">{{ old("physio_services.$index.description_en", $service['description_en'] ?? '') }}</textarea>
                                    </label>
                                    <label class="full">Description (Bangla)
                                        <textarea name="physio_services[{{ $index }}][description_bn]" rows="3">{{ old("physio_services.$index.description_bn", $service['description_bn'] ?? '') }}</textarea>
                                    </label>
                                    <label>CTA Label (English)
                                        <input type="text" name="physio_services[{{ $index }}][cta_label_en]" value="{{ old("physio_services.$index.cta_label_en", $service['cta_label_en'] ?? '') }}">
                                    </label>
                                    <label>CTA Label (Bangla)
                                        <input type="text" name="physio_services[{{ $index }}][cta_label_bn]" value="{{ old("physio_services.$index.cta_label_bn", $service['cta_label_bn'] ?? '') }}">
                                    </label>
                                    <label class="full">CTA URL
                                        <input type="text" name="physio_services[{{ $index }}][cta_url]" value="{{ old("physio_services.$index.cta_url", $service['cta_url'] ?? '') }}" placeholder="/en#appointment">
                                    </label>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <template data-physio-template>
                        <article class="physio-card" data-physio-card>
                            <header>
                                <strong>Service __INDEX__</strong>
                                <button type="button" class="danger-link" data-remove-physio>Remove</button>
                            </header>
                            <div class="physio-card-grid">
                                <label>Display Order
                                    <input type="number" name="physio_services[__INDEX__][display_order]" value="__ORDER__" min="0">
                                </label>
                                <label class="check"><input type="checkbox" name="physio_services[__INDEX__][is_active]" value="1" checked> Active</label>
                                <label class="check"><input type="checkbox" name="physio_services[__INDEX__][featured]" value="1"> Featured</label>
                                <label class="full">Title (English)
                                    <input type="text" name="physio_services[__INDEX__][title_en]" required>
                                </label>
                                <label class="full">Title (Bangla)
                                    <input type="text" name="physio_services[__INDEX__][title_bn]" required>
                                </label>
                                <label class="full">Description (English)
                                    <textarea name="physio_services[__INDEX__][description_en]" rows="3"></textarea>
                                </label>
                                <label class="full">Description (Bangla)
                                    <textarea name="physio_services[__INDEX__][description_bn]" rows="3"></textarea>
                                </label>
                                <label>CTA Label (English)
                                    <input type="text" name="physio_services[__INDEX__][cta_label_en]">
                                </label>
                                <label>CTA Label (Bangla)
                                    <input type="text" name="physio_services[__INDEX__][cta_label_bn]">
                                </label>
                                <label class="full">CTA URL
                                    <input type="text" name="physio_services[__INDEX__][cta_url]" placeholder="/en#appointment">
                                </label>
                            </div>
                        </article>
                    </template>

                    <button type="button" class="physio-add" data-add-physio>Add service</button>
                </div>

                <button type="submit">Save Physiotherapy Section</button>
            </form>

            <div class="panel builder-preview">
                <h2 style="margin-top:0;">Preview</h2>
                @include('partials.public.home-section', [
                    'section' => $section,
                    'preview' => true,
                ])
            </div>
        @elseif ($section->section_key === 'specialist-doctors')
            <form class="panel builder-panel" method="POST" action="{{ route('admin.homepage.update', $section) }}" enctype="multipart/form-data" data-doctor-form>
                @csrf
                @method('PUT')

                <h2 style="margin-top:0;">Specialist Doctors Builder</h2>
                <p class="muted">Use doctor cards to power the public carousel.</p>
                <input type="hidden" name="section_type" value="cards">

                <div class="subpanel">
                    <h3>Section settings</h3>
                    <div class="doctor-settings-grid">
                        <label>Section Label (English)
                            <input type="text" name="section_label_en" value="{{ old('section_label_en', $section->section_label_en) }}" required>
                        </label>
                        <label>Section Label (Bangla)
                            <input type="text" name="section_label_bn" value="{{ old('section_label_bn', $section->section_label_bn) }}" required>
                        </label>
                        <label>Display Order
                            <input type="number" name="display_order" value="{{ old('display_order', $section->display_order) }}" min="0" required>
                        </label>
                        <label>Carousel Speed (ms)
                            <input type="number" name="carousel_speed" value="{{ old('carousel_speed', $section->carousel_speed) }}" min="500" step="100">
                        </label>
                        <label>Cards per View (Desktop)
                            <input type="number" name="doctor_settings[cards_per_view_desktop]" value="{{ old('doctor_settings.cards_per_view_desktop', $doctorData['cards_per_view_desktop'] ?? 3) }}" min="1" max="6">
                        </label>
                        <label>Cards per View (Mobile)
                            <input type="number" name="doctor_settings[cards_per_view_mobile]" value="{{ old('doctor_settings.cards_per_view_mobile', $doctorData['cards_per_view_mobile'] ?? 1) }}" min="1" max="2">
                        </label>
                        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Active</label>
                        <label class="check"><input type="checkbox" name="preview_enabled" value="1" @checked(old('preview_enabled', $section->preview_enabled))> Preview before publishing</label>
                        <label class="check"><input type="checkbox" name="doctor_settings[auto_scroll]" value="1" @checked(old('doctor_settings.auto_scroll', $doctorData['auto_scroll'] ?? true))> Optional auto-scroll</label>
                        <label class="check"><input type="checkbox" name="doctor_settings[pause_on_hover]" value="1" @checked(old('doctor_settings.pause_on_hover', $doctorData['pause_on_hover'] ?? true))> Pause on hover</label>
                        <label class="check"><input type="checkbox" name="doctor_settings[show_arrows]" value="1" @checked(old('doctor_settings.show_arrows', $doctorData['show_arrows'] ?? true))> Arrow controls</label>
                        <label class="check"><input type="checkbox" name="doctor_settings[show_dots]" value="1" @checked(old('doctor_settings.show_dots', $doctorData['show_dots'] ?? false))> Dot indicators</label>
                        <label class="check"><input type="checkbox" name="doctor_settings[loop]" value="1" @checked(old('doctor_settings.loop', $doctorData['loop'] ?? true))> Loop carousel</label>
                        <label>Display Limit
                            <input type="number" name="display_limit" value="{{ old('display_limit', $section->display_limit) }}" min="1">
                        </label>
                    </div>
                </div>

                <div class="subpanel">
                    <h3>Doctor cards</h3>
                    <p class="muted-hint">Each card supports photo, mobile photo, titles, schedule, profile, appointment, and optional call button.</p>
                    <div class="doctor-builder" data-doctor-builder>
                        @foreach ($doctorCards as $index => $doctor)
                            @php
                                $doctor = is_array($doctor) ? $doctor : [];
                            @endphp
                            <article class="doctor-card" data-doctor-card>
                                <header>
                                    <strong>Doctor {{ $loop->iteration }}</strong>
                                    <button type="button" class="danger-link" data-remove-doctor>Remove</button>
                                </header>
                                <div class="doctor-card-grid">
                                    <label>Display Order
                                        <input type="number" name="doctor_cards[{{ $index }}][display_order]" value="{{ old("doctor_cards.$index.display_order", $doctor['display_order'] ?? $loop->iteration) }}" min="0">
                                    </label>
                                    <label class="check"><input type="checkbox" name="doctor_cards[{{ $index }}][is_active]" value="1" @checked(old("doctor_cards.$index.is_active", $doctor['is_active'] ?? true))> Active</label>
                                    <label class="check"><input type="checkbox" name="doctor_cards[{{ $index }}][featured]" value="1" @checked(old("doctor_cards.$index.featured", $doctor['featured'] ?? false))> Featured</label>
                                    <label class="check"><input type="checkbox" name="doctor_cards[{{ $index }}][call_enabled]" value="1" @checked(old("doctor_cards.$index.call_enabled", $doctor['call_enabled'] ?? false))> Call button enabled</label>
                                    <label class="full">Name (English)
                                        <input type="text" name="doctor_cards[{{ $index }}][name_en]" value="{{ old("doctor_cards.$index.name_en", $doctor['name_en'] ?? '') }}" required>
                                    </label>
                                    <label class="full">Name (Bangla)
                                        <input type="text" name="doctor_cards[{{ $index }}][name_bn]" value="{{ old("doctor_cards.$index.name_bn", $doctor['name_bn'] ?? '') }}" required>
                                    </label>
                                    <label class="full">Degrees
                                        <input type="text" name="doctor_cards[{{ $index }}][degrees]" value="{{ old("doctor_cards.$index.degrees", $doctor['degrees'] ?? '') }}" placeholder="MBBS, FCPS">
                                    </label>
                                    <label>Specialty
                                        <input type="text" name="doctor_cards[{{ $index }}][specialty]" value="{{ old("doctor_cards.$index.specialty", $doctor['specialty'] ?? '') }}">
                                    </label>
                                    <label>Department
                                        <input type="text" name="doctor_cards[{{ $index }}][department]" value="{{ old("doctor_cards.$index.department", $doctor['department'] ?? '') }}">
                                    </label>
                                    <label class="full">Available Schedule (English)
                                        <input type="text" name="doctor_cards[{{ $index }}][schedule_en]" value="{{ old("doctor_cards.$index.schedule_en", $doctor['schedule_en'] ?? '') }}" placeholder="Sat-Thu | 10:00 AM - 2:00 PM">
                                    </label>
                                    <label class="full">Available Schedule (Bangla)
                                        <input type="text" name="doctor_cards[{{ $index }}][schedule_bn]" value="{{ old("doctor_cards.$index.schedule_bn", $doctor['schedule_bn'] ?? '') }}" placeholder="শনিবার- বৃহস্পতিবার | সকাল ১০টা - দুপুর ২টা">
                                    </label>
                                    <label class="full">Profile URL
                                        <input type="text" name="doctor_cards[{{ $index }}][profile_url]" value="{{ old("doctor_cards.$index.profile_url", $doctor['profile_url'] ?? '') }}" placeholder="/en#doctors">
                                    </label>
                                    <label class="full">Book Appointment URL
                                        <input type="text" name="doctor_cards[{{ $index }}][appointment_url]" value="{{ old("doctor_cards.$index.appointment_url", $doctor['appointment_url'] ?? '') }}" placeholder="/en#appointment">
                                    </label>
                                    <label class="full">Call URL
                                        <input type="text" name="doctor_cards[{{ $index }}][call_url]" value="{{ old("doctor_cards.$index.call_url", $doctor['call_url'] ?? '') }}" placeholder="tel:+8801...">
                                    </label>
                                    <label>Desktop Photo
                                        <input type="file" name="doctor_cards[{{ $index }}][desktop_image_file]" accept="image/*">
                                        <input type="hidden" name="doctor_cards[{{ $index }}][desktop_image_path]" value="{{ $doctor['desktop_image_path'] ?? '' }}">
                                        @if (! empty($doctor['desktop_image_path']))
                                            <div class="doctor-preview"><img src="{{ asset('storage/'.$doctor['desktop_image_path']) }}" alt="Doctor photo"></div>
                                        @endif
                                    </label>
                                    <label>Mobile Photo
                                        <input type="file" name="doctor_cards[{{ $index }}][mobile_image_file]" accept="image/*">
                                        <input type="hidden" name="doctor_cards[{{ $index }}][mobile_image_path]" value="{{ $doctor['mobile_image_path'] ?? '' }}">
                                        @if (! empty($doctor['mobile_image_path']))
                                            <div class="doctor-preview"><img src="{{ asset('storage/'.$doctor['mobile_image_path']) }}" alt="Doctor mobile photo"></div>
                                        @endif
                                    </label>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <template data-doctor-template>
                        <article class="doctor-card" data-doctor-card>
                            <header>
                                <strong>Doctor __INDEX__</strong>
                                <button type="button" class="danger-link" data-remove-doctor>Remove</button>
                            </header>
                            <div class="doctor-card-grid">
                                <label>Display Order
                                    <input type="number" name="doctor_cards[__INDEX__][display_order]" value="__ORDER__" min="0">
                                </label>
                                <label class="check"><input type="checkbox" name="doctor_cards[__INDEX__][is_active]" value="1" checked> Active</label>
                                <label class="check"><input type="checkbox" name="doctor_cards[__INDEX__][featured]" value="1"> Featured</label>
                                <label class="check"><input type="checkbox" name="doctor_cards[__INDEX__][call_enabled]" value="1"> Call button enabled</label>
                                <label class="full">Name (English)
                                    <input type="text" name="doctor_cards[__INDEX__][name_en]" required>
                                </label>
                                <label class="full">Name (Bangla)
                                    <input type="text" name="doctor_cards[__INDEX__][name_bn]" required>
                                </label>
                                <label class="full">Degrees
                                    <input type="text" name="doctor_cards[__INDEX__][degrees]" placeholder="MBBS, FCPS">
                                </label>
                                <label>Specialty
                                    <input type="text" name="doctor_cards[__INDEX__][specialty]">
                                </label>
                                <label>Department
                                    <input type="text" name="doctor_cards[__INDEX__][department]">
                                </label>
                                <label class="full">Available Schedule (English)
                                    <input type="text" name="doctor_cards[__INDEX__][schedule_en]" placeholder="Sat-Thu | 10:00 AM - 2:00 PM">
                                </label>
                                <label class="full">Available Schedule (Bangla)
                                    <input type="text" name="doctor_cards[__INDEX__][schedule_bn]" placeholder="শনিবার- বৃহস্পতিবার | সকাল ১০টা - দুপুর ২টা">
                                </label>
                                <label class="full">Profile URL
                                    <input type="text" name="doctor_cards[__INDEX__][profile_url]" placeholder="/en#doctors">
                                </label>
                                <label class="full">Book Appointment URL
                                    <input type="text" name="doctor_cards[__INDEX__][appointment_url]" placeholder="/en#appointment">
                                </label>
                                <label class="full">Call URL
                                    <input type="text" name="doctor_cards[__INDEX__][call_url]" placeholder="tel:+8801...">
                                </label>
                                <label>Desktop Photo
                                    <input type="file" name="doctor_cards[__INDEX__][desktop_image_file]" accept="image/*">
                                    <input type="hidden" name="doctor_cards[__INDEX__][desktop_image_path]">
                                </label>
                                <label>Mobile Photo
                                    <input type="file" name="doctor_cards[__INDEX__][mobile_image_file]" accept="image/*">
                                    <input type="hidden" name="doctor_cards[__INDEX__][mobile_image_path]">
                                </label>
                            </div>
                        </article>
                    </template>

                    <button type="button" class="doctor-add" data-add-doctor>Add doctor</button>
                </div>

                <button type="submit">Save Doctors Section</button>
            </form>
        @elseif ($section->section_key === 'hero-slider')
            <form class="panel builder-panel" method="POST" action="{{ route('admin.homepage.update', $section) }}" enctype="multipart/form-data" data-hero-form data-upload-limit-mb="20">
                @csrf
                @method('PUT')

                <h2 style="margin-top:0;">Hero Image Builder</h2>
                <p class="muted">Slides are sample content until the admin replaces them.</p>
                <input type="hidden" name="section_type" value="slider">

                <div class="subpanel">
                    <h3>Section settings</h3>
                    <div class="hero-settings-grid">
                        <label>Section Label (English)
                            <input type="text" name="section_label_en" value="{{ old('section_label_en', $section->section_label_en) }}" required>
                        </label>
                        <label>Section Label (Bangla)
                            <input type="text" name="section_label_bn" value="{{ old('section_label_bn', $section->section_label_bn) }}" required>
                        </label>
                        <label>Display Order
                            <input type="number" name="display_order" value="{{ old('display_order', $section->display_order) }}" min="0" required>
                        </label>
                        <label>Carousel Speed (ms)
                            <input type="number" name="carousel_speed" value="{{ old('carousel_speed', $section->carousel_speed) }}" min="500" step="100">
                        </label>
                        <label>Overlay Opacity (%)
                            <input type="number" name="hero_settings[overlay_opacity]" value="{{ old('hero_settings.overlay_opacity', $heroData['overlay_opacity'] ?? 60) }}" min="0" max="100">
                        </label>
                        <label>Text Alignment
                            <select name="hero_settings[text_alignment]">
                                @foreach (['left' => 'Left', 'center' => 'Center', 'right' => 'Right'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('hero_settings.text_alignment', $heroData['text_alignment'] ?? 'left') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Active</label>
                        <label class="check"><input type="checkbox" name="preview_enabled" value="1" @checked(old('preview_enabled', $section->preview_enabled))> Preview before publishing</label>
                        <label class="check"><input type="checkbox" name="hero_settings[auto_play]" value="1" @checked(old('hero_settings.auto_play', $heroData['auto_play'] ?? true))> Auto-play</label>
                        <label class="check"><input type="checkbox" name="hero_settings[pause_on_hover]" value="1" @checked(old('hero_settings.pause_on_hover', $heroData['pause_on_hover'] ?? true))> Pause on hover</label>
                        <label class="check"><input type="checkbox" name="hero_settings[swipe]" value="1" @checked(old('hero_settings.swipe', $heroData['swipe'] ?? true))> Swipe support</label>
                        <label class="check"><input type="checkbox" name="hero_settings[keyboard]" value="1" @checked(old('hero_settings.keyboard', $heroData['keyboard'] ?? true))> Keyboard support</label>
                        <label class="check"><input type="checkbox" name="hero_settings[dots]" value="1" @checked(old('hero_settings.dots', $heroData['dots'] ?? true))> Dot indicators</label>
                        <label class="check"><input type="checkbox" name="hero_settings[arrows]" value="1" @checked(old('hero_settings.arrows', $heroData['arrows'] ?? true))> Previous and next buttons</label>
                        <label class="check"><input type="checkbox" name="hero_settings[lazy_load_after_first_slide]" value="1" @checked(old('hero_settings.lazy_load_after_first_slide', $heroData['lazy_load_after_first_slide'] ?? true))> Lazy-load after first slide</label>
                    </div>
                </div>

                <div class="subpanel">
                    <h3>Slides</h3>
                    <p class="muted-hint">Upload, edit, or remove desktop and mobile hero images. Image files can be up to 20 MB each. Keep title fields blank when you want an image-only hero.</p>

                    <div class="slide-builder" data-slide-builder>
                        @foreach ($heroSlides as $index => $slide)
                            @php
                                $slide = is_array($slide) ? $slide : [];
                            @endphp
                            <article class="slide-card" data-slide-card>
                                <header>
                                    <strong>Slide {{ $loop->iteration }}</strong>
                                    <button type="button" class="danger-link" data-remove-slide>Remove</button>
                                </header>
                                <div class="slide-grid">
                                    <label>Display Order
                                        <input type="number" name="hero_slides[{{ $index }}][display_order]" value="{{ old("hero_slides.$index.display_order", $slide['display_order'] ?? $loop->iteration) }}" min="0">
                                    </label>
                                    <label>Publish Date
                                        <input type="datetime-local" name="hero_slides[{{ $index }}][publish_at]" value="{{ old("hero_slides.$index.publish_at", isset($slide['publish_at']) && $slide['publish_at'] ? \Illuminate\Support\Carbon::parse($slide['publish_at'])->format('Y-m-d\TH:i') : '') }}">
                                    </label>
                                    <label>Expiry Date
                                        <input type="datetime-local" name="hero_slides[{{ $index }}][expires_at]" value="{{ old("hero_slides.$index.expires_at", isset($slide['expires_at']) && $slide['expires_at'] ? \Illuminate\Support\Carbon::parse($slide['expires_at'])->format('Y-m-d\TH:i') : '') }}">
                                    </label>
                                    <label class="check"><input type="checkbox" name="hero_slides[{{ $index }}][is_active]" value="1" @checked(old("hero_slides.$index.is_active", $slide['is_active'] ?? true))> Active</label>
                                    <label>Slide Alignment
                                        <select name="hero_slides[{{ $index }}][text_alignment]">
                                            @foreach (['left' => 'Left', 'center' => 'Center', 'right' => 'Right'] as $value => $label)
                                                <option value="{{ $value }}" @selected(old("hero_slides.$index.text_alignment", $slide['text_alignment'] ?? $heroData['text_alignment'] ?? 'left') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label>Overlay Opacity (%)
                                        <input type="number" name="hero_slides[{{ $index }}][overlay_opacity]" value="{{ old("hero_slides.$index.overlay_opacity", $slide['overlay_opacity'] ?? ($heroData['overlay_opacity'] ?? 60)) }}" min="0" max="100">
                                    </label>
                                    <label class="full">Title (English)
                                        <input type="text" name="hero_slides[{{ $index }}][title_en]" value="{{ old("hero_slides.$index.title_en", $slide['title_en'] ?? '') }}">
                                    </label>
                                    <label class="full">Title (Bangla)
                                        <input type="text" name="hero_slides[{{ $index }}][title_bn]" value="{{ old("hero_slides.$index.title_bn", $slide['title_bn'] ?? '') }}">
                                    </label>
                                    <label class="full">Subtitle (English)
                                        <textarea name="hero_slides[{{ $index }}][subtitle_en]" rows="2">{{ old("hero_slides.$index.subtitle_en", $slide['subtitle_en'] ?? '') }}</textarea>
                                    </label>
                                    <label class="full">Subtitle (Bangla)
                                        <textarea name="hero_slides[{{ $index }}][subtitle_bn]" rows="2">{{ old("hero_slides.$index.subtitle_bn", $slide['subtitle_bn'] ?? '') }}</textarea>
                                    </label>
                                    <label>Primary CTA Label (English)
                                        <input type="text" name="hero_slides[{{ $index }}][primary_cta_label_en]" value="{{ old("hero_slides.$index.primary_cta_label_en", $slide['primary_cta_label_en'] ?? '') }}">
                                    </label>
                                    <label>Primary CTA Label (Bangla)
                                        <input type="text" name="hero_slides[{{ $index }}][primary_cta_label_bn]" value="{{ old("hero_slides.$index.primary_cta_label_bn", $slide['primary_cta_label_bn'] ?? '') }}">
                                    </label>
                                    <label>Primary CTA URL
                                        <input type="text" name="hero_slides[{{ $index }}][primary_cta_url]" value="{{ old("hero_slides.$index.primary_cta_url", $slide['primary_cta_url'] ?? '') }}" placeholder="/en#appointment">
                                    </label>
                                    <label>Secondary CTA Label (English)
                                        <input type="text" name="hero_slides[{{ $index }}][secondary_cta_label_en]" value="{{ old("hero_slides.$index.secondary_cta_label_en", $slide['secondary_cta_label_en'] ?? '') }}">
                                    </label>
                                    <label>Secondary CTA Label (Bangla)
                                        <input type="text" name="hero_slides[{{ $index }}][secondary_cta_label_bn]" value="{{ old("hero_slides.$index.secondary_cta_label_bn", $slide['secondary_cta_label_bn'] ?? '') }}">
                                    </label>
                                    <label>Secondary CTA URL
                                        <input type="text" name="hero_slides[{{ $index }}][secondary_cta_url]" value="{{ old("hero_slides.$index.secondary_cta_url", $slide['secondary_cta_url'] ?? '') }}" placeholder="/en#services">
                                    </label>
                                    <label>Desktop Image
                                        <input type="file" name="hero_slides[{{ $index }}][desktop_image_file]" accept="image/*">
                                        <input type="hidden" name="hero_slides[{{ $index }}][desktop_image_path]" value="{{ $slide['desktop_image_path'] ?? '' }}">
                                        @if (! empty($slide['desktop_image_path']))
                                            <div class="slide-preview"><img src="{{ asset('storage/'.$slide['desktop_image_path']) }}" alt="Desktop slide image"></div>
                                            <label class="check"><input type="checkbox" name="hero_slides[{{ $index }}][remove_desktop_image]" value="1"> Delete current desktop image</label>
                                        @endif
                                    </label>
                                    <label>Mobile Image
                                        <input type="file" name="hero_slides[{{ $index }}][mobile_image_file]" accept="image/*">
                                        <input type="hidden" name="hero_slides[{{ $index }}][mobile_image_path]" value="{{ $slide['mobile_image_path'] ?? '' }}">
                                        @if (! empty($slide['mobile_image_path']))
                                            <div class="slide-preview"><img src="{{ asset('storage/'.$slide['mobile_image_path']) }}" alt="Mobile slide image"></div>
                                            <label class="check"><input type="checkbox" name="hero_slides[{{ $index }}][remove_mobile_image]" value="1"> Delete current mobile image</label>
                                        @endif
                                    </label>
                                </div>
                                <div class="slide-actions">
                                    <span class="muted-hint">Sample slide data can stay until real content is entered.</span>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <template data-slide-template>
                        <article class="slide-card" data-slide-card>
                            <header>
                                <strong>Slide __INDEX__</strong>
                                <button type="button" class="danger-link" data-remove-slide>Remove</button>
                            </header>
                            <div class="slide-grid">
                                <label>Display Order
                                    <input type="number" name="hero_slides[__INDEX__][display_order]" value="__ORDER__" min="0">
                                </label>
                                <label>Publish Date
                                    <input type="datetime-local" name="hero_slides[__INDEX__][publish_at]">
                                </label>
                                <label>Expiry Date
                                    <input type="datetime-local" name="hero_slides[__INDEX__][expires_at]">
                                </label>
                                <label class="check"><input type="checkbox" name="hero_slides[__INDEX__][is_active]" value="1" checked> Active</label>
                                <label>Slide Alignment
                                    <select name="hero_slides[__INDEX__][text_alignment]">
                                        <option value="left">Left</option>
                                        <option value="center">Center</option>
                                        <option value="right">Right</option>
                                    </select>
                                </label>
                                <label>Overlay Opacity (%)
                                    <input type="number" name="hero_slides[__INDEX__][overlay_opacity]" value="{{ $heroData['overlay_opacity'] ?? 60 }}" min="0" max="100">
                                </label>
                                <label class="full">Title (English)
                                    <input type="text" name="hero_slides[__INDEX__][title_en]">
                                </label>
                                <label class="full">Title (Bangla)
                                    <input type="text" name="hero_slides[__INDEX__][title_bn]">
                                </label>
                                <label class="full">Subtitle (English)
                                    <textarea name="hero_slides[__INDEX__][subtitle_en]" rows="2"></textarea>
                                </label>
                                <label class="full">Subtitle (Bangla)
                                    <textarea name="hero_slides[__INDEX__][subtitle_bn]" rows="2"></textarea>
                                </label>
                                <label>Primary CTA Label (English)
                                    <input type="text" name="hero_slides[__INDEX__][primary_cta_label_en]">
                                </label>
                                <label>Primary CTA Label (Bangla)
                                    <input type="text" name="hero_slides[__INDEX__][primary_cta_label_bn]">
                                </label>
                                <label>Primary CTA URL
                                    <input type="text" name="hero_slides[__INDEX__][primary_cta_url]" placeholder="/en#appointment">
                                </label>
                                <label>Secondary CTA Label (English)
                                    <input type="text" name="hero_slides[__INDEX__][secondary_cta_label_en]">
                                </label>
                                <label>Secondary CTA Label (Bangla)
                                    <input type="text" name="hero_slides[__INDEX__][secondary_cta_label_bn]">
                                </label>
                                <label>Secondary CTA URL
                                    <input type="text" name="hero_slides[__INDEX__][secondary_cta_url]" placeholder="/en#services">
                                </label>
                                <label>Desktop Image
                                    <input type="file" name="hero_slides[__INDEX__][desktop_image_file]" accept="image/*">
                                    <input type="hidden" name="hero_slides[__INDEX__][desktop_image_path]">
                                </label>
                                <label>Mobile Image
                                    <input type="file" name="hero_slides[__INDEX__][mobile_image_file]" accept="image/*">
                                    <input type="hidden" name="hero_slides[__INDEX__][mobile_image_path]">
                                </label>
                            </div>
                        </article>
                    </template>

                    <button type="button" class="hero-add" data-add-slide>Add slide</button>
                </div>

                <button type="submit">Save Hero Image</button>
            </form>

            <div class="panel builder-preview">
                <h2 style="margin-top:0;">Preview</h2>
                @include('partials.public.home-section', [
                    'section' => $section,
                    'preview' => true,
                ])
            </div>
        @else
            <form class="panel form-grid form-wide" method="POST" action="{{ route('admin.homepage.update', $section) }}" enctype="multipart/form-data" data-homepage-general-form data-upload-limit-mb="{{ $runtimeUploadLimitMb }}">
                @csrf
                @method('PUT')

                <h2 style="margin-top:0;">{{ $section->section_label_en }}</h2>
                <p class="muted">{{ $section->section_key }} · {{ $section->section_type }}</p>

                <label>Section Label (English)
                    <input type="text" name="section_label_en" value="{{ old('section_label_en', $section->section_label_en) }}" required>
                </label>
                <label>Section Label (Bangla)
                    <input type="text" name="section_label_bn" value="{{ old('section_label_bn', $section->section_label_bn) }}" required>
                </label>
                <label>Section Type
                    <select name="section_type">
                        @foreach (['layout','slider','cards','actions','strip','points','stats','cta'] as $type)
                            <option value="{{ $type }}" @selected(old('section_type', $section->section_type) === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Display Order
                    <input type="number" name="display_order" value="{{ old('display_order', $section->display_order) }}" min="0" required>
                </label>
                <label>Background Color
                    <input type="text" name="background_color" value="{{ old('background_color', $section->background_color) }}" placeholder="#f5f7fb">
                </label>
                <label>Title (English)
                    <input type="text" name="title_en" value="{{ old('title_en', $section->title_en) }}">
                </label>
                <label>Title (Bangla)
                    <input type="text" name="title_bn" value="{{ old('title_bn', $section->title_bn) }}">
                </label>
                <label>Subtitle (English)
                    <input type="text" name="subtitle_en" value="{{ old('subtitle_en', $section->subtitle_en) }}">
                </label>
                <label>Subtitle (Bangla)
                    <input type="text" name="subtitle_bn" value="{{ old('subtitle_bn', $section->subtitle_bn) }}">
                </label>
                <label>Summary (English)
                    <textarea name="summary_en" rows="3">{{ old('summary_en', $section->summary_en) }}</textarea>
                </label>
                <label>Summary (Bangla)
                    <textarea name="summary_bn" rows="3">{{ old('summary_bn', $section->summary_bn) }}</textarea>
                </label>
                <label>Content (English)
                    <textarea name="content_en" rows="4">{{ old('content_en', $section->content_en) }}</textarea>
                </label>
                <label>Content (Bangla)
                    <textarea name="content_bn" rows="4">{{ old('content_bn', $section->content_bn) }}</textarea>
                </label>
                <label>Section Data JSON
                    <textarea name="section_data_json" rows="10">{{ old('section_data_json', $jsonValue) }}</textarea>
                </label>
                <label>Related Doctor Refs JSON
                    <textarea name="related_doctor_refs_json" rows="5">{{ old('related_doctor_refs_json', $doctorRefs) }}</textarea>
                </label>
                <label>Related Service Refs JSON
                    <textarea name="related_service_refs_json" rows="5">{{ old('related_service_refs_json', $serviceRefs) }}</textarea>
                </label>
                <label>Related Test Refs JSON
                    <textarea name="related_test_refs_json" rows="5">{{ old('related_test_refs_json', $testRefs) }}</textarea>
                </label>
                <label>Related Post Refs JSON
                    <textarea name="related_post_refs_json" rows="5">{{ old('related_post_refs_json', $postRefs) }}</textarea>
                </label>
                <label>Background Image
                    <input type="file" name="background_image_path" accept="image/*">
                </label>
                <label class="full">Shared Section Image
                    <input type="file" name="desktop_image_path" accept="image/*">
                    <span class="muted-hint">One image is used for English, Bangla, desktop, and mobile. Current server limit: {{ $runtimeUploadLimitMb }} MB.</span>
                    @if ($section->desktop_image_path)
                        <span class="muted-hint">Current image: <a href="{{ asset('storage/'.$section->desktop_image_path) }}" target="_blank" rel="noopener">view</a></span>
                    @endif
                </label>
                <label>Accent Image
                    <input type="file" name="accent_image_path" accept="image/*">
                </label>
                <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Active</label>
                <label class="check"><input type="checkbox" name="auto_scroll" value="1" @checked(old('auto_scroll', $section->auto_scroll))> Auto-scroll</label>
                <label>Carousel Speed (ms)
                    <input type="number" name="carousel_speed" value="{{ old('carousel_speed', $section->carousel_speed) }}" min="500" step="100">
                </label>
                <label>Display Limit
                    <input type="number" name="display_limit" value="{{ old('display_limit', $section->display_limit) }}" min="1">
                </label>
                @if ($runtimeUploadLimitMb < 50)
                    <div class="upload-limit-warning">
                        PHP is currently allowing image uploads up to {{ $runtimeUploadLimitMb }} MB. Larger files will fail before Laravel can save them. The local server should be started with a higher `upload_max_filesize` and `post_max_size`.
                    </div>
                @endif

                <button type="submit">Save Section</button>
            </form>
        @endif
    </div>

    @if ($section->section_key === 'physiotherapy-services')
        <script>
            (function () {
                const builder = document.querySelector('[data-physio-builder]');
                const template = document.querySelector('[data-physio-template]');
                const addButton = document.querySelector('[data-add-physio]');

                if (! builder || ! template || ! addButton) {
                    return;
                }

                const nextIndex = () => builder.querySelectorAll('[data-physio-card]').length;

                const refresh = () => {
                    builder.querySelectorAll('[data-physio-card]').forEach((card, index) => {
                        const heading = card.querySelector('header strong');
                        if (heading) {
                            heading.textContent = `Service ${index + 1}`;
                        }
                        card.querySelectorAll('input, select, textarea').forEach((field) => {
                            if (field.name) {
                                field.name = field.name.replace(/physio_services\[\d+\]/, `physio_services[${index}]`);
                            }
                        });
                    });
                };

                addButton.addEventListener('click', () => {
                    const index = nextIndex();
                    const html = template.innerHTML
                        .replaceAll('__INDEX__', index)
                        .replaceAll('__ORDER__', index + 1);

                    builder.insertAdjacentHTML('beforeend', html);
                    refresh();
                });

                builder.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-remove-physio]');
                    if (! button) {
                        return;
                    }

                    const card = button.closest('[data-physio-card]');
                    if (card) {
                        card.remove();
                        refresh();
                    }
                });
            })();
        </script>
    @endif

    @if ($section->section_key === 'specialist-doctors')
        <script>
            (function () {
                const builder = document.querySelector('[data-doctor-builder]');
                const template = document.querySelector('[data-doctor-template]');
                const addButton = document.querySelector('[data-add-doctor]');

                if (! builder || ! template || ! addButton) {
                    return;
                }

                const nextIndex = () => builder.querySelectorAll('[data-doctor-card]').length;

                const refresh = () => {
                    builder.querySelectorAll('[data-doctor-card]').forEach((card, index) => {
                        const heading = card.querySelector('header strong');
                        if (heading) {
                            heading.textContent = `Doctor ${index + 1}`;
                        }
                        card.querySelectorAll('input, select, textarea').forEach((field) => {
                            if (field.name) {
                                field.name = field.name.replace(/doctor_cards\[\d+\]/, `doctor_cards[${index}]`);
                            }
                        });
                    });
                };

                addButton.addEventListener('click', () => {
                    const index = nextIndex();
                    const html = template.innerHTML
                        .replaceAll('__INDEX__', index)
                        .replaceAll('__ORDER__', index + 1);

                    builder.insertAdjacentHTML('beforeend', html);
                    refresh();
                });

                builder.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-remove-doctor]');
                    if (! button) {
                        return;
                    }

                    const card = button.closest('[data-doctor-card]');
                    if (card) {
                        card.remove();
                        refresh();
                    }
                });
            })();
        </script>
    @endif

    @if ($section->section_key === 'hero-slider')
        <script>
            (function () {
                const builder = document.querySelector('[data-slide-builder]');
                const template = document.querySelector('[data-slide-template]');
                const addButton = document.querySelector('[data-add-slide]');

                if (! builder || ! template || ! addButton) {
                    return;
                }

                const form = document.querySelector('[data-hero-form]');
                const maxUploadMb = Number(form?.dataset.uploadLimitMb || 20);
                const maxUploadBytes = maxUploadMb * 1024 * 1024;
                const nextIndex = () => builder.querySelectorAll('[data-slide-card]').length;

                const refresh = () => {
                    builder.querySelectorAll('[data-slide-card]').forEach((card, index) => {
                        const heading = card.querySelector('header strong');
                        if (heading) {
                            heading.textContent = `Slide ${index + 1}`;
                        }
                        card.querySelectorAll('input, select, textarea').forEach((field) => {
                            if (field.name) {
                                field.name = field.name.replace(/hero_slides\[\d+\]/, `hero_slides[${index}]`);
                            }
                        });
                    });
                };

                addButton.addEventListener('click', () => {
                    const index = nextIndex();
                    const html = template.innerHTML
                        .replaceAll('__INDEX__', index)
                        .replaceAll('__ORDER__', index + 1);

                    builder.insertAdjacentHTML('beforeend', html);
                    refresh();
                });

                builder.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-remove-slide]');
                    if (! button) {
                        return;
                    }

                    const card = button.closest('[data-slide-card]');
                    if (card) {
                        card.remove();
                        refresh();
                    }
                });

                form?.addEventListener('submit', (event) => {
                    const oversized = Array.from(form.querySelectorAll('input[type="file"]')).find((field) => {
                        return field.files && field.files[0] && field.files[0].size > maxUploadBytes;
                    });

                    if (! oversized) {
                        return;
                    }

                    event.preventDefault();
                    oversized.focus();
                    alert(`"${oversized.files[0].name}" is too large. Please upload an image up to ${maxUploadMb} MB.`);
                });
            })();
        </script>
    @endif

    <script>
        (function () {
            const form = document.querySelector('[data-homepage-general-form]');

            if (! form) {
                return;
            }

            const maxUploadMb = Number(form.dataset.uploadLimitMb || 2);
            const maxUploadBytes = maxUploadMb * 1024 * 1024;

            form.addEventListener('submit', (event) => {
                const oversized = Array.from(form.querySelectorAll('input[type="file"]')).find((field) => {
                    return field.files && field.files[0] && field.files[0].size > maxUploadBytes;
                });

                if (! oversized) {
                    return;
                }

                event.preventDefault();
                oversized.focus();
                alert(`"${oversized.files[0].name}" is too large for the current PHP server limit. Please upload an image up to ${maxUploadMb} MB, or restart the local server with a higher upload limit.`);
            });
        })();
    </script>
@endsection
