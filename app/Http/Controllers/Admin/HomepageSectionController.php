<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Support\AuditLogger;
use App\Support\HomepageBuilder;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HomepageSectionController extends Controller
{
    public function index(): View
    {
        $this->ensureDefaults();

        return view('admin.homepage.index', [
            'sections' => HomepageSection::withTrashed()->orderBy('display_order')->get(),
            'definitions' => HomepageBuilder::sections(),
        ]);
    }

    public function edit(HomepageSection $homepageSection): View
    {
        $this->ensureDefaults();

        return view('admin.homepage.edit', [
            'section' => $homepageSection,
            'definitions' => HomepageBuilder::sections(),
            'previewData' => $this->previewData($homepageSection),
        ]);
    }

    public function update(Request $request, HomepageSection $homepageSection): RedirectResponse
    {
        $oldValues = $homepageSection->toArray();

        if ($homepageSection->section_key === 'hero-slider') {
            $validated = $this->validateHeroSection($request);
            $sectionData = $this->buildHeroSectionData($request, $homepageSection);

            $homepageSection->fill([
                'section_label_en' => $validated['section_label_en'],
                'section_label_bn' => $validated['section_label_bn'],
                'section_type' => $validated['section_type'],
                'display_order' => $validated['display_order'],
                'background_color' => $validated['background_color'] ?? null,
                'title_en' => $validated['title_en'] ?? null,
                'title_bn' => $validated['title_bn'] ?? null,
                'subtitle_en' => $validated['subtitle_en'] ?? null,
                'subtitle_bn' => $validated['subtitle_bn'] ?? null,
                'summary_en' => $validated['summary_en'] ?? null,
                'summary_bn' => $validated['summary_bn'] ?? null,
                'content_en' => $validated['content_en'] ?? null,
                'content_bn' => $validated['content_bn'] ?? null,
                'section_data' => $sectionData,
                'is_active' => $request->boolean('is_active'),
                'preview_enabled' => $request->boolean('preview_enabled'),
                'auto_scroll' => (bool) ($sectionData['settings']['auto_play'] ?? false),
                'carousel_speed' => $validated['carousel_speed'] ?? 4500,
            ])->save();

            AuditLogger::record('homepage_section_updated', $homepageSection, $oldValues, $homepageSection->toArray(), request: $request);

            return redirect()->route('admin.homepage.edit', $homepageSection)->with('status', 'Homepage section updated.');
        }

        if ($homepageSection->section_key === 'specialist-doctors') {
            $validated = $this->validateDoctorSection($request);
            $sectionData = $this->buildDoctorSectionData($request, $homepageSection);

            $homepageSection->fill([
                'section_label_en' => $validated['section_label_en'],
                'section_label_bn' => $validated['section_label_bn'],
                'section_type' => $validated['section_type'],
                'display_order' => $validated['display_order'],
                'background_color' => $validated['background_color'] ?? null,
                'title_en' => $validated['title_en'] ?? null,
                'title_bn' => $validated['title_bn'] ?? null,
                'subtitle_en' => $validated['subtitle_en'] ?? null,
                'subtitle_bn' => $validated['subtitle_bn'] ?? null,
                'summary_en' => $validated['summary_en'] ?? null,
                'summary_bn' => $validated['summary_bn'] ?? null,
                'content_en' => $validated['content_en'] ?? null,
                'content_bn' => $validated['content_bn'] ?? null,
                'section_data' => $sectionData,
                'is_active' => $request->boolean('is_active'),
                'preview_enabled' => $request->boolean('preview_enabled'),
                'auto_scroll' => (bool) ($sectionData['settings']['auto_scroll'] ?? false),
                'carousel_speed' => $validated['carousel_speed'] ?? 4500,
                'display_limit' => $validated['display_limit'] ?? null,
            ])->save();

            AuditLogger::record('homepage_section_updated', $homepageSection, $oldValues, $homepageSection->toArray(), request: $request);

            return redirect()->route('admin.homepage.edit', $homepageSection)->with('status', 'Homepage section updated.');
        }

        if ($homepageSection->section_key === 'physiotherapy-services') {
            $validated = $this->validatePhysiotherapySection($request);
            $sectionData = $this->buildPhysiotherapySectionData($request);

            $homepageSection->fill([
                'section_label_en' => $validated['section_label_en'],
                'section_label_bn' => $validated['section_label_bn'],
                'section_type' => $validated['section_type'],
                'display_order' => $validated['display_order'],
                'background_color' => $validated['background_color'] ?? null,
                'title_en' => $validated['title_en'] ?? null,
                'title_bn' => $validated['title_bn'] ?? null,
                'subtitle_en' => $validated['subtitle_en'] ?? null,
                'subtitle_bn' => $validated['subtitle_bn'] ?? null,
                'summary_en' => $validated['summary_en'] ?? null,
                'summary_bn' => $validated['summary_bn'] ?? null,
                'content_en' => $validated['content_en'] ?? null,
                'content_bn' => $validated['content_bn'] ?? null,
                'section_data' => $sectionData,
                'is_active' => $request->boolean('is_active'),
                'preview_enabled' => $request->boolean('preview_enabled'),
                'display_limit' => $validated['display_limit'] ?? null,
            ])->save();

            AuditLogger::record('homepage_section_updated', $homepageSection, $oldValues, $homepageSection->toArray(), request: $request);

            return redirect()->route('admin.homepage.edit', $homepageSection)->with('status', 'Homepage section updated.');
        }

        $validated = $this->validateSection($request);

        foreach ([
            'background_image_path',
            'desktop_image_path',
            'mobile_image_path',
            'accent_image_path',
        ] as $field) {
            if ($request->hasFile($field)) {
                if ($homepageSection->{$field}) {
                    Storage::disk('public')->delete($homepageSection->{$field});
                }

                $validated[$field] = $request->file($field)->store('homepage', 'public');
            } else {
                unset($validated[$field]);
            }
        }

        $validated['section_data'] = $this->decodeJsonField($request->input('section_data_json'));
        $validated['related_doctor_refs'] = $this->decodeJsonField($request->input('related_doctor_refs_json'));
        $validated['related_service_refs'] = $this->decodeJsonField($request->input('related_service_refs_json'));
        $validated['related_test_refs'] = $this->decodeJsonField($request->input('related_test_refs_json'));
        $validated['related_post_refs'] = $this->decodeJsonField($request->input('related_post_refs_json'));

        foreach (['is_active', 'auto_scroll', 'preview_enabled'] as $booleanField) {
            $validated[$booleanField] = $request->boolean($booleanField);
        }

        $homepageSection->update($validated);

        AuditLogger::record('homepage_section_updated', $homepageSection, $oldValues, $homepageSection->toArray(), request: $request);

        return redirect()->route('admin.homepage.edit', $homepageSection)->with('status', 'Homepage section updated.');
    }

    public function toggle(Request $request, HomepageSection $homepageSection): RedirectResponse
    {
        $homepageSection->update(['is_active' => ! $homepageSection->is_active]);
        AuditLogger::record('homepage_section_status_changed', $homepageSection, request: $request);

        return back()->with('status', 'Homepage section status updated.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['integer'],
        ]);

        foreach ($validated['orders'] as $id => $order) {
            HomepageSection::whereKey($id)->update(['display_order' => $order]);
        }

        AuditLogger::record('homepage_sections_reordered', metadata: ['orders' => $validated['orders']], request: $request);

        return back()->with('status', 'Homepage section order saved.');
    }

    private function ensureDefaults(): void
    {
        $definitions = HomepageBuilder::sections();

        foreach ($definitions as $key => $definition) {
            $section = HomepageSection::firstOrCreate(
                ['section_key' => $key],
                [
                    'section_label_en' => $definition['label_en'],
                    'section_label_bn' => $definition['label_bn'],
                    'section_type' => $definition['type'],
                    'display_order' => array_search($key, array_keys($definitions), true) ?: 0,
                    'is_active' => true,
                    'preview_enabled' => true,
                    'section_data' => HomepageBuilder::defaultContent($key),
                ]
            );

            if ($key === 'hero-slider') {
                $normalized = $this->normalizeHeroSectionData($section->section_data ?? []);

                if ($normalized !== ($section->section_data ?? [])) {
                    $section->update(['section_data' => $normalized]);
                }
            }

            if ($key === 'specialist-doctors') {
                $normalized = $this->normalizeDoctorSectionData($section->section_data ?? []);

                if ($normalized !== ($section->section_data ?? [])) {
                    $section->update(['section_data' => $normalized]);
                }
            }

            if ($key === 'physiotherapy-services') {
                $normalized = $this->normalizePhysiotherapySectionData($section->section_data ?? []);

                if ($normalized !== ($section->section_data ?? [])) {
                    $section->update(['section_data' => $normalized]);
                }
            }
        }
    }

    private function validateHeroSection(Request $request): array
    {
        return $request->validate([
            'section_label_en' => ['required', 'string', 'max:255'],
            'section_label_bn' => ['required', 'string', 'max:255'],
            'section_type' => ['required', 'string', Rule::in(['slider'])],
            'display_order' => ['required', 'integer', 'min:0'],
            'background_color' => ['nullable', 'string', 'max:50'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_bn' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'subtitle_bn' => ['nullable', 'string', 'max:255'],
            'summary_en' => ['nullable', 'string'],
            'summary_bn' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'content_bn' => ['nullable', 'string'],
            'carousel_speed' => ['nullable', 'integer', 'min:500', 'max:20000'],
            'preview_enabled' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'hero_settings.auto_play' => ['nullable', 'boolean'],
            'hero_settings.pause_on_hover' => ['nullable', 'boolean'],
            'hero_settings.swipe' => ['nullable', 'boolean'],
            'hero_settings.keyboard' => ['nullable', 'boolean'],
            'hero_settings.dots' => ['nullable', 'boolean'],
            'hero_settings.arrows' => ['nullable', 'boolean'],
            'hero_settings.lazy_load_after_first_slide' => ['nullable', 'boolean'],
            'hero_settings.overlay_opacity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'hero_settings.text_alignment' => ['nullable', 'string', Rule::in(['left', 'center', 'right'])],
            'hero_slides' => ['required', 'array', 'min:1'],
            'hero_slides.*.display_order' => ['nullable', 'integer', 'min:0'],
            'hero_slides.*.is_active' => ['nullable', 'boolean'],
            'hero_slides.*.title_en' => ['required', 'string', 'max:255'],
            'hero_slides.*.title_bn' => ['required', 'string', 'max:255'],
            'hero_slides.*.subtitle_en' => ['nullable', 'string', 'max:255'],
            'hero_slides.*.subtitle_bn' => ['nullable', 'string', 'max:255'],
            'hero_slides.*.primary_cta_label_en' => ['nullable', 'string', 'max:255'],
            'hero_slides.*.primary_cta_label_bn' => ['nullable', 'string', 'max:255'],
            'hero_slides.*.primary_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_slides.*.secondary_cta_label_en' => ['nullable', 'string', 'max:255'],
            'hero_slides.*.secondary_cta_label_bn' => ['nullable', 'string', 'max:255'],
            'hero_slides.*.secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_slides.*.publish_at' => ['nullable', 'date'],
            'hero_slides.*.expires_at' => ['nullable', 'date'],
            'hero_slides.*.overlay_opacity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'hero_slides.*.text_alignment' => ['nullable', 'string', Rule::in(['left', 'center', 'right'])],
            'hero_slides.*.desktop_image_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'hero_slides.*.mobile_image_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'hero_slides.*.desktop_image_path' => ['nullable', 'string', 'max:255'],
            'hero_slides.*.mobile_image_path' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function buildHeroSectionData(Request $request, HomepageSection $homepageSection): array
    {
        $existing = $this->normalizeHeroSectionData($homepageSection->section_data ?? []);
        $settings = array_merge($existing['settings'] ?? [], array_filter([
            'auto_play' => $request->boolean('hero_settings.auto_play'),
            'pause_on_hover' => $request->boolean('hero_settings.pause_on_hover'),
            'swipe' => $request->boolean('hero_settings.swipe'),
            'keyboard' => $request->boolean('hero_settings.keyboard'),
            'dots' => $request->boolean('hero_settings.dots'),
            'arrows' => $request->boolean('hero_settings.arrows'),
            'lazy_load_after_first_slide' => $request->boolean('hero_settings.lazy_load_after_first_slide'),
            'overlay_opacity' => $request->input('hero_settings.overlay_opacity'),
            'text_alignment' => $request->input('hero_settings.text_alignment'),
        ], static fn ($value) => $value !== null));

        $slides = [];
        foreach ((array) $request->input('hero_slides', []) as $index => $slide) {
            $existingSlide = $existing['slides'][$index] ?? [];

            $desktopImage = $this->storeUploadedFile($request, "hero_slides.$index.desktop_image_file", 'homepage', $existingSlide['desktop_image_path'] ?? ($slide['desktop_image_path'] ?? null));
            $mobileImage = $this->storeUploadedFile($request, "hero_slides.$index.mobile_image_file", 'homepage', $existingSlide['mobile_image_path'] ?? ($slide['mobile_image_path'] ?? null));

            $slides[] = array_filter([
                'display_order' => isset($slide['display_order']) ? (int) $slide['display_order'] : ($index + 1),
                'is_active' => array_key_exists('is_active', $slide) ? (bool) $slide['is_active'] : true,
                'media_type' => 'image',
                'title_en' => $slide['title_en'] ?? null,
                'title_bn' => $slide['title_bn'] ?? null,
                'subtitle_en' => $slide['subtitle_en'] ?? null,
                'subtitle_bn' => $slide['subtitle_bn'] ?? null,
                'primary_cta_label_en' => $slide['primary_cta_label_en'] ?? null,
                'primary_cta_label_bn' => $slide['primary_cta_label_bn'] ?? null,
                'primary_cta_url' => $slide['primary_cta_url'] ?? null,
                'secondary_cta_label_en' => $slide['secondary_cta_label_en'] ?? null,
                'secondary_cta_label_bn' => $slide['secondary_cta_label_bn'] ?? null,
                'secondary_cta_url' => $slide['secondary_cta_url'] ?? null,
                'publish_at' => $slide['publish_at'] ?? null,
                'expires_at' => $slide['expires_at'] ?? null,
                'overlay_opacity' => isset($slide['overlay_opacity']) ? (int) $slide['overlay_opacity'] : null,
                'text_alignment' => $slide['text_alignment'] ?? null,
                'desktop_image_path' => $desktopImage,
                'mobile_image_path' => $mobileImage,
            ], static fn ($value) => ! blank($value) || $value === 0 || $value === false);
        }

        usort($slides, static fn (array $left, array $right): int => ($left['display_order'] ?? 0) <=> ($right['display_order'] ?? 0));

        return [
            'settings' => $settings,
            'slides' => array_values($slides),
        ];
    }

    private function validateDoctorSection(Request $request): array
    {
        return $request->validate([
            'section_label_en' => ['required', 'string', 'max:255'],
            'section_label_bn' => ['required', 'string', 'max:255'],
            'section_type' => ['required', 'string', Rule::in(['cards'])],
            'display_order' => ['required', 'integer', 'min:0'],
            'background_color' => ['nullable', 'string', 'max:50'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_bn' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'subtitle_bn' => ['nullable', 'string', 'max:255'],
            'summary_en' => ['nullable', 'string'],
            'summary_bn' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'content_bn' => ['nullable', 'string'],
            'carousel_speed' => ['nullable', 'integer', 'min:500', 'max:20000'],
            'display_limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'preview_enabled' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'doctor_settings.auto_scroll' => ['nullable', 'boolean'],
            'doctor_settings.pause_on_hover' => ['nullable', 'boolean'],
            'doctor_settings.show_arrows' => ['nullable', 'boolean'],
            'doctor_settings.show_dots' => ['nullable', 'boolean'],
            'doctor_settings.loop' => ['nullable', 'boolean'],
            'doctor_settings.cards_per_view_desktop' => ['nullable', 'integer', 'min:1', 'max:6'],
            'doctor_settings.cards_per_view_mobile' => ['nullable', 'integer', 'min:1', 'max:2'],
            'doctor_cards' => ['required', 'array', 'min:1'],
            'doctor_cards.*.display_order' => ['nullable', 'integer', 'min:0'],
            'doctor_cards.*.is_active' => ['nullable', 'boolean'],
            'doctor_cards.*.featured' => ['nullable', 'boolean'],
            'doctor_cards.*.call_enabled' => ['nullable', 'boolean'],
            'doctor_cards.*.name_en' => ['required', 'string', 'max:255'],
            'doctor_cards.*.name_bn' => ['required', 'string', 'max:255'],
            'doctor_cards.*.degrees' => ['nullable', 'string', 'max:255'],
            'doctor_cards.*.specialty' => ['nullable', 'string', 'max:255'],
            'doctor_cards.*.department' => ['nullable', 'string', 'max:255'],
            'doctor_cards.*.schedule_en' => ['nullable', 'string', 'max:255'],
            'doctor_cards.*.schedule_bn' => ['nullable', 'string', 'max:255'],
            'doctor_cards.*.profile_url' => ['nullable', 'string', 'max:255'],
            'doctor_cards.*.appointment_url' => ['nullable', 'string', 'max:255'],
            'doctor_cards.*.call_url' => ['nullable', 'string', 'max:255'],
            'doctor_cards.*.desktop_image_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'doctor_cards.*.mobile_image_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'doctor_cards.*.desktop_image_path' => ['nullable', 'string', 'max:255'],
            'doctor_cards.*.mobile_image_path' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function buildDoctorSectionData(Request $request, HomepageSection $homepageSection): array
    {
        $existing = $this->normalizeDoctorSectionData($homepageSection->section_data ?? []);
        $settings = array_merge($existing['settings'] ?? [], array_filter([
            'auto_scroll' => $request->boolean('doctor_settings.auto_scroll'),
            'pause_on_hover' => $request->boolean('doctor_settings.pause_on_hover'),
            'show_arrows' => $request->boolean('doctor_settings.show_arrows'),
            'show_dots' => $request->boolean('doctor_settings.show_dots'),
            'loop' => $request->boolean('doctor_settings.loop'),
            'cards_per_view_desktop' => $request->input('doctor_settings.cards_per_view_desktop'),
            'cards_per_view_mobile' => $request->input('doctor_settings.cards_per_view_mobile'),
        ], static fn ($value) => $value !== null));

        $cards = [];
        foreach ((array) $request->input('doctor_cards', []) as $index => $card) {
            $existingCard = $existing['doctors'][$index] ?? [];

            $desktopImage = $this->storeUploadedFile($request, "doctor_cards.$index.desktop_image_file", 'homepage', $existingCard['desktop_image_path'] ?? ($card['desktop_image_path'] ?? null));
            $mobileImage = $this->storeUploadedFile($request, "doctor_cards.$index.mobile_image_file", 'homepage', $existingCard['mobile_image_path'] ?? ($card['mobile_image_path'] ?? null));

            $cards[] = array_filter([
                'display_order' => isset($card['display_order']) ? (int) $card['display_order'] : ($index + 1),
                'is_active' => array_key_exists('is_active', $card) ? (bool) $card['is_active'] : true,
                'featured' => array_key_exists('featured', $card) ? (bool) $card['featured'] : false,
                'call_enabled' => array_key_exists('call_enabled', $card) ? (bool) $card['call_enabled'] : false,
                'name_en' => $card['name_en'] ?? null,
                'name_bn' => $card['name_bn'] ?? null,
                'degrees' => $card['degrees'] ?? null,
                'specialty' => $card['specialty'] ?? null,
                'department' => $card['department'] ?? null,
                'schedule_en' => $card['schedule_en'] ?? null,
                'schedule_bn' => $card['schedule_bn'] ?? null,
                'profile_url' => $card['profile_url'] ?? null,
                'appointment_url' => $card['appointment_url'] ?? null,
                'call_url' => $card['call_url'] ?? null,
                'desktop_image_path' => $desktopImage,
                'mobile_image_path' => $mobileImage,
            ], static fn ($value) => ! blank($value) || $value === 0 || $value === false);
        }

        usort($cards, static fn (array $left, array $right): int => ($left['display_order'] ?? 0) <=> ($right['display_order'] ?? 0));

        return [
            'settings' => $settings,
            'doctors' => array_values($cards),
        ];
    }

    private function normalizeDoctorSectionData(array $data): array
    {
        $defaults = HomepageBuilder::defaultContent('specialist-doctors');
        $settings = array_merge($defaults['settings'], $data['settings'] ?? []);
        $doctors = $data['doctors'] ?? $defaults['doctors'];

        if (! is_array($doctors) || $doctors === []) {
            $doctors = $defaults['doctors'];
        }

        $normalizedDoctors = [];
        foreach ($doctors as $index => $doctor) {
            $normalizedDoctors[] = array_merge([
                'display_order' => $index + 1,
                'is_active' => true,
                'featured' => false,
                'call_enabled' => false,
                'name_en' => '',
                'name_bn' => '',
                'degrees' => '',
                'specialty' => '',
                'department' => '',
                'schedule_en' => '',
                'schedule_bn' => '',
                'profile_url' => '',
                'appointment_url' => '',
                'call_url' => '',
                'desktop_image_path' => null,
                'mobile_image_path' => null,
            ], is_array($doctor) ? $doctor : []);
        }

        return [
            'settings' => $settings,
            'doctors' => $normalizedDoctors,
        ];
    }

    private function validatePhysiotherapySection(Request $request): array
    {
        return $request->validate([
            'section_label_en' => ['required', 'string', 'max:255'],
            'section_label_bn' => ['required', 'string', 'max:255'],
            'section_type' => ['required', 'string', Rule::in(['cards'])],
            'display_order' => ['required', 'integer', 'min:0'],
            'background_color' => ['nullable', 'string', 'max:50'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_bn' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'subtitle_bn' => ['nullable', 'string', 'max:255'],
            'summary_en' => ['nullable', 'string'],
            'summary_bn' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'content_bn' => ['nullable', 'string'],
            'display_limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'preview_enabled' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'physio_settings.columns_desktop' => ['nullable', 'integer', 'min:1', 'max:6'],
            'physio_settings.columns_tablet' => ['nullable', 'integer', 'min:1', 'max:4'],
            'physio_settings.columns_mobile' => ['nullable', 'integer', 'min:1', 'max:2'],
            'physio_settings.show_icons' => ['nullable', 'boolean'],
            'physio_settings.show_cta' => ['nullable', 'boolean'],
            'physio_services' => ['required', 'array', 'min:1'],
            'physio_services.*.display_order' => ['nullable', 'integer', 'min:0'],
            'physio_services.*.is_active' => ['nullable', 'boolean'],
            'physio_services.*.featured' => ['nullable', 'boolean'],
            'physio_services.*.title_en' => ['required', 'string', 'max:255'],
            'physio_services.*.title_bn' => ['required', 'string', 'max:255'],
            'physio_services.*.description_en' => ['nullable', 'string'],
            'physio_services.*.description_bn' => ['nullable', 'string'],
            'physio_services.*.cta_label_en' => ['nullable', 'string', 'max:255'],
            'physio_services.*.cta_label_bn' => ['nullable', 'string', 'max:255'],
            'physio_services.*.cta_url' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function buildPhysiotherapySectionData(Request $request): array
    {
        $settings = array_filter([
            'columns_desktop' => $request->input('physio_settings.columns_desktop'),
            'columns_tablet' => $request->input('physio_settings.columns_tablet'),
            'columns_mobile' => $request->input('physio_settings.columns_mobile'),
            'show_icons' => $request->boolean('physio_settings.show_icons'),
            'show_cta' => $request->boolean('physio_settings.show_cta'),
        ], static fn ($value) => $value !== null);

        $services = [];
        foreach ((array) $request->input('physio_services', []) as $index => $service) {
            $services[] = array_filter([
                'display_order' => isset($service['display_order']) ? (int) $service['display_order'] : ($index + 1),
                'is_active' => array_key_exists('is_active', $service) ? (bool) $service['is_active'] : true,
                'featured' => array_key_exists('featured', $service) ? (bool) $service['featured'] : false,
                'title_en' => $service['title_en'] ?? null,
                'title_bn' => $service['title_bn'] ?? null,
                'description_en' => $service['description_en'] ?? null,
                'description_bn' => $service['description_bn'] ?? null,
                'cta_label_en' => $service['cta_label_en'] ?? null,
                'cta_label_bn' => $service['cta_label_bn'] ?? null,
                'cta_url' => $service['cta_url'] ?? null,
            ], static fn ($value) => ! blank($value) || $value === 0 || $value === false);
        }

        usort($services, static fn (array $left, array $right): int => ($left['display_order'] ?? 0) <=> ($right['display_order'] ?? 0));

        return [
            'settings' => $settings,
            'services' => array_values($services),
        ];
    }

    private function normalizePhysiotherapySectionData(array $data): array
    {
        $defaults = HomepageBuilder::defaultContent('physiotherapy-services');
        $settings = array_merge($defaults['settings'], $data['settings'] ?? []);
        $services = $data['services'] ?? $data['items'] ?? $defaults['services'];

        if (! is_array($services) || $services === []) {
            $services = $defaults['services'];
        }

        $normalizedServices = [];
        foreach ($services as $index => $service) {
            $normalizedServices[] = array_merge([
                'display_order' => $index + 1,
                'is_active' => true,
                'featured' => false,
                'title_en' => '',
                'title_bn' => '',
                'description_en' => '',
                'description_bn' => '',
                'cta_label_en' => '',
                'cta_label_bn' => '',
                'cta_url' => '',
            ], is_array($service) ? $service : []);
        }

        return [
            'settings' => $settings,
            'services' => $normalizedServices,
        ];
    }

    private function normalizeHeroSectionData(array $data): array
    {
        $defaults = HomepageBuilder::defaultContent('hero-slider');
        $settings = array_merge($defaults['settings'], $data['settings'] ?? []);
        $slides = $data['slides'] ?? $defaults['slides'];

        if (! is_array($slides) || $slides === []) {
            $slides = $defaults['slides'];
        }

        $normalizedSlides = [];
        foreach ($slides as $index => $slide) {
            $normalizedSlides[] = array_merge([
                'display_order' => $index + 1,
                'is_active' => true,
                'media_type' => 'image',
                'title_en' => '',
                'title_bn' => '',
                'subtitle_en' => '',
                'subtitle_bn' => '',
                'primary_cta_label_en' => '',
                'primary_cta_label_bn' => '',
                'primary_cta_url' => '',
                'secondary_cta_label_en' => '',
                'secondary_cta_label_bn' => '',
                'secondary_cta_url' => '',
                'publish_at' => null,
                'expires_at' => null,
                'overlay_opacity' => null,
                'text_alignment' => null,
                'desktop_image_path' => null,
                'mobile_image_path' => null,
            ], is_array($slide) ? $slide : []);

            $normalizedSlides[$index]['media_type'] = 'image';
            unset($normalizedSlides[$index]['video_url'], $normalizedSlides[$index]['video_path']);
        }

        return [
            'settings' => $settings,
            'slides' => $normalizedSlides,
        ];
    }

    private function storeUploadedFile(Request $request, string $field, string $directory, ?string $current = null): ?string
    {
        if (! $request->hasFile($field)) {
            return $current;
        }

        $file = $request->file($field);

        if (! $file instanceof UploadedFile) {
            return $current;
        }

        if ($current) {
            Storage::disk('public')->delete($current);
        }

        return $file->store($directory, 'public');
    }

    private function validateSection(Request $request): array
    {
        return $request->validate([
            'section_label_en' => ['required', 'string', 'max:255'],
            'section_label_bn' => ['required', 'string', 'max:255'],
            'section_type' => ['required', 'string', Rule::in(['layout', 'slider', 'cards', 'actions', 'strip', 'points', 'stats', 'cta'])],
            'display_order' => ['required', 'integer', 'min:0'],
            'background_color' => ['nullable', 'string', 'max:50'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_bn' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'subtitle_bn' => ['nullable', 'string', 'max:255'],
            'summary_en' => ['nullable', 'string'],
            'summary_bn' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'content_bn' => ['nullable', 'string'],
            'section_data_json' => ['nullable', 'string'],
            'related_doctor_refs_json' => ['nullable', 'string'],
            'related_service_refs_json' => ['nullable', 'string'],
            'related_test_refs_json' => ['nullable', 'string'],
            'related_post_refs_json' => ['nullable', 'string'],
            'auto_scroll' => ['nullable', 'boolean'],
            'carousel_speed' => ['nullable', 'integer', 'min:500', 'max:20000'],
            'display_limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'preview_enabled' => ['nullable', 'boolean'],
            'background_image_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'desktop_image_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'mobile_image_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'accent_image_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);
    }

    private function decodeJsonField(mixed $value): ?array
    {
        if (blank($value)) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function previewData(HomepageSection $section): array
    {
        return [
            'title' => app()->getLocale() === 'bn' ? ($section->title_bn ?: $section->section_label_bn) : ($section->title_en ?: $section->section_label_en),
            'subtitle' => app()->getLocale() === 'bn' ? $section->subtitle_bn : $section->subtitle_en,
            'summary' => app()->getLocale() === 'bn' ? $section->summary_bn : $section->summary_en,
            'content' => app()->getLocale() === 'bn' ? $section->content_bn : $section->content_en,
            'data' => $section->section_data ?? [],
        ];
    }
}
