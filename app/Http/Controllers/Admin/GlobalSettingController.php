<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GlobalSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'setting' => GlobalSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = GlobalSetting::current();

        $validated = $request->validate([
            'hospital_name_en' => ['required', 'string', 'max:255'],
            'hospital_name_bn' => ['required', 'string', 'max:255'],
            'logo_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:4096'],
            'logo_mobile_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:4096'],
            'favicon_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:2048'],
            'remove_logo_path' => ['nullable', 'boolean'],
            'remove_logo_mobile_path' => ['nullable', 'boolean'],
            'remove_favicon_path' => ['nullable', 'boolean'],
            'address_en' => ['nullable', 'string'],
            'address_bn' => ['nullable', 'string'],
            'phone_primary' => ['nullable', 'string', 'max:50'],
            'phone_secondary' => ['nullable', 'string', 'max:50'],
            'emergency_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'opening_hours_en' => ['nullable', 'string'],
            'opening_hours_bn' => ['nullable', 'string'],
            'social_links' => ['nullable', 'array'],
            'social_links.facebook' => ['nullable', 'url'],
            'social_links.youtube' => ['nullable', 'url'],
            'social_links.instagram' => ['nullable', 'url'],
            'social_links.linkedin' => ['nullable', 'url'],
            'social_links.x' => ['nullable', 'url'],
            'social_links_dynamic' => ['nullable', 'array'],
            'social_links_dynamic.*.platform' => ['nullable', 'string', 'max:50', 'regex:/^[a-z0-9_-]+$/i'],
            'social_links_dynamic.*.url' => ['nullable', 'url', 'max:255'],
            'social_links_dynamic.*.remove' => ['nullable', 'boolean'],
            'whatsapp_link' => ['nullable', 'url'],
            'google_map_embed' => ['nullable', 'string'],
            'seo_settings' => ['nullable', 'array'],
            'seo_settings.meta_title_en' => ['nullable', 'string', 'max:255'],
            'seo_settings.meta_title_bn' => ['nullable', 'string', 'max:255'],
            'seo_settings.meta_description_en' => ['nullable', 'string', 'max:500'],
            'seo_settings.meta_description_bn' => ['nullable', 'string', 'max:500'],
            'seo_settings.meta_keywords_en' => ['nullable', 'string', 'max:500'],
            'seo_settings.meta_keywords_bn' => ['nullable', 'string', 'max:500'],
            'seo_settings.canonical_url' => ['nullable', 'url', 'max:255'],
            'seo_settings.robots' => ['nullable', 'string', 'max:120'],
            'seo_settings.google_site_verification' => ['nullable', 'string', 'max:255'],
            'seo_settings.bing_site_verification' => ['nullable', 'string', 'max:255'],
            'seo_settings.facebook_domain_verification' => ['nullable', 'string', 'max:255'],
            'seo_og_image_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'remove_seo_og_image' => ['nullable', 'boolean'],
            'marketing_tools' => ['nullable', 'array'],
            'marketing_tools.*.name' => ['nullable', 'string', 'max:120'],
            'marketing_tools.*.provider' => ['nullable', 'string', 'max:80'],
            'marketing_tools.*.tracking_id' => ['nullable', 'string', 'max:255'],
            'marketing_tools.*.script_head' => ['nullable', 'string'],
            'marketing_tools.*.script_body' => ['nullable', 'string'],
            'marketing_tools.*.is_active' => ['nullable', 'boolean'],
            'marketing_tools.*.remove' => ['nullable', 'boolean'],
            'sms_settings' => ['nullable', 'array'],
            'sms_settings.enabled' => ['nullable', 'boolean'],
            'sms_settings.provider' => ['nullable', 'string', 'max:120'],
            'sms_settings.sender_id' => ['nullable', 'string', 'max:120'],
            'sms_settings.api_base_url' => ['nullable', 'url', 'max:255'],
            'sms_settings.api_key' => ['nullable', 'string', 'max:255'],
            'sms_settings.api_secret' => ['nullable', 'string', 'max:255'],
            'sms_settings.test_number' => ['nullable', 'string', 'max:50'],
            'email_integration_settings' => ['nullable', 'array'],
            'email_integration_settings.enabled' => ['nullable', 'boolean'],
            'email_integration_settings.provider' => ['nullable', 'string', 'max:120'],
            'email_integration_settings.from_name' => ['nullable', 'string', 'max:255'],
            'email_integration_settings.from_email' => ['nullable', 'email', 'max:255'],
            'email_integration_settings.host' => ['nullable', 'string', 'max:255'],
            'email_integration_settings.port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'email_integration_settings.encryption' => ['nullable', 'string', 'max:20'],
            'email_integration_settings.username' => ['nullable', 'string', 'max:255'],
            'email_integration_settings.password' => ['nullable', 'string', 'max:255'],
            'email_integration_settings.api_key' => ['nullable', 'string', 'max:255'],
            'email_integration_settings.reply_to' => ['nullable', 'email', 'max:255'],
            'visitor_tracking_settings' => ['nullable', 'array'],
            'visitor_tracking_settings.enabled' => ['nullable', 'boolean'],
            'visitor_tracking_settings.anonymize_ip' => ['nullable', 'boolean'],
            'visitor_tracking_settings.active_window_minutes' => ['nullable', 'integer', 'min:1', 'max:120'],
            'visitor_tracking_settings.retain_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'visitor_tracking_settings.cookie_consent_enabled' => ['nullable', 'boolean'],
            'visitor_tracking_settings.cookie_banner_text_en' => ['nullable', 'string', 'max:500'],
            'visitor_tracking_settings.cookie_banner_text_bn' => ['nullable', 'string', 'max:500'],
            'visitor_tracking_settings.cookie_privacy_url' => ['nullable', 'string', 'max:255'],
            'visitor_tracking_settings.web_vitals_enabled' => ['nullable', 'boolean'],
            'default_language' => ['required', Rule::in(['en', 'bn'])],
            'contact_buttons_visible' => ['nullable', 'boolean'],
            'header_top_bar_visible' => ['nullable', 'boolean'],
            'newsletter_visible' => ['nullable', 'boolean'],
            'book_appointment_button_label_en' => ['nullable', 'string', 'max:255'],
            'book_appointment_button_label_bn' => ['nullable', 'string', 'max:255'],
            'book_appointment_button_url' => ['nullable', 'string', 'max:255'],
            'emergency_button_label_en' => ['nullable', 'string', 'max:255'],
            'emergency_button_label_bn' => ['nullable', 'string', 'max:255'],
            'emergency_button_url' => ['nullable', 'string', 'max:255'],
            'footer_description_en' => ['nullable', 'string'],
            'footer_description_bn' => ['nullable', 'string'],
            'copyright_text_en' => ['nullable', 'string'],
            'copyright_text_bn' => ['nullable', 'string'],
        ]);

        $oldValues = $setting->only([
            'hospital_name_en',
            'hospital_name_bn',
            'logo_path',
            'logo_mobile_path',
            'favicon_path',
            'default_language',
            'contact_buttons_visible',
            'header_top_bar_visible',
            'newsletter_visible',
            'seo_settings',
            'marketing_tools',
            'sms_settings',
            'email_integration_settings',
            'visitor_tracking_settings',
        ]);

        foreach (['logo_path', 'logo_mobile_path', 'favicon_path'] as $field) {
            if ($request->hasFile($field)) {
                $this->deleteUploadedAsset($setting->{$field});

                $validated[$field] = $request->file($field)->store('site-settings', 'public');
            } elseif ($request->boolean('remove_'.$field)) {
                $this->deleteUploadedAsset($setting->{$field});
                $validated[$field] = null;
            } else {
                unset($validated[$field]);
            }
        }

        $validated['contact_buttons_visible'] = $request->boolean('contact_buttons_visible');
        $validated['header_top_bar_visible'] = $request->boolean('header_top_bar_visible');
        $validated['newsletter_visible'] = $request->boolean('newsletter_visible');
        $validated['social_links'] = $this->normalizeSocialLinks($request->input('social_links_dynamic', $validated['social_links'] ?? []));
        $validated['seo_settings'] = $this->buildSeoSettings($request, $setting);
        $validated['marketing_tools'] = ['tools' => $this->normalizeMarketingTools($request->input('marketing_tools', []))];
        $validated['sms_settings'] = $this->normalizeSmsSettings($request->input('sms_settings', []));
        $validated['email_integration_settings'] = $this->normalizeEmailIntegrationSettings($request->input('email_integration_settings', []));
        $validated['visitor_tracking_settings'] = $this->normalizeVisitorTrackingSettings($request->input('visitor_tracking_settings', []));

        unset(
            $validated['remove_logo_path'],
            $validated['remove_logo_mobile_path'],
            $validated['remove_favicon_path'],
            $validated['social_links_dynamic'],
            $validated['seo_og_image_file'],
            $validated['remove_seo_og_image'],
        );

        $setting->update($validated);

        AuditLogger::record(
            'global_settings_updated',
            $setting,
            $oldValues,
            $setting->only($oldValues ? array_keys($oldValues) : []),
            request: $request
        );

        return back()->with('status', 'Global settings updated successfully.');
    }

    private function deleteUploadedAsset(?string $path): void
    {
        if ($path && str_starts_with($path, 'site-settings/')) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @param  array<int|string, array<string, mixed>|string|null>  $rows
     * @return array<string, string>|null
     */
    private function normalizeSocialLinks(array $rows): ?array
    {
        $links = [];

        foreach ($rows as $platform => $row) {
            if (is_array($row)) {
                if (! empty($row['remove'])) {
                    continue;
                }

                $platform = (string) ($row['platform'] ?? '');
                $url = (string) ($row['url'] ?? '');
            } else {
                $url = (string) $row;
            }

            $platform = strtolower(trim((string) $platform));
            $url = trim($url);

            if ($platform !== '' && $url !== '') {
                $links[$platform] = $url;
            }
        }

        return $links ?: null;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSeoSettings(Request $request, GlobalSetting $setting): array
    {
        $seo = array_merge($setting->seo_settings ?? [], (array) $request->input('seo_settings', []));

        if ($request->hasFile('seo_og_image_file')) {
            $this->deleteUploadedAsset($seo['og_image_path'] ?? null);
            $seo['og_image_path'] = $request->file('seo_og_image_file')->store('site-settings', 'public');
        } elseif ($request->boolean('remove_seo_og_image')) {
            $this->deleteUploadedAsset($seo['og_image_path'] ?? null);
            $seo['og_image_path'] = null;
        }

        return $seo;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function normalizeMarketingTools(array $rows): array
    {
        $tools = [];

        foreach ($rows as $row) {
            if (! is_array($row) || ! empty($row['remove'])) {
                continue;
            }

            $name = trim((string) ($row['name'] ?? ''));
            $provider = trim((string) ($row['provider'] ?? ''));

            if ($name === '' && $provider === '') {
                continue;
            }

            $tools[] = [
                'name' => $name,
                'provider' => $provider,
                'tracking_id' => trim((string) ($row['tracking_id'] ?? '')),
                'script_head' => (string) ($row['script_head'] ?? ''),
                'script_body' => (string) ($row['script_body'] ?? ''),
                'is_active' => ! empty($row['is_active']),
            ];
        }

        return $tools;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private function normalizeSmsSettings(array $settings): array
    {
        $settings['enabled'] = ! empty($settings['enabled']);

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private function normalizeEmailIntegrationSettings(array $settings): array
    {
        $settings['enabled'] = ! empty($settings['enabled']);

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private function normalizeVisitorTrackingSettings(array $settings): array
    {
        return [
            'enabled' => ! empty($settings['enabled']),
            'anonymize_ip' => ! empty($settings['anonymize_ip']),
            'active_window_minutes' => (int) ($settings['active_window_minutes'] ?? 5),
            'retain_days' => (int) ($settings['retain_days'] ?? 365),
            'cookie_consent_enabled' => ! empty($settings['cookie_consent_enabled']),
            'cookie_banner_text_en' => (string) ($settings['cookie_banner_text_en'] ?? ''),
            'cookie_banner_text_bn' => (string) ($settings['cookie_banner_text_bn'] ?? ''),
            'cookie_privacy_url' => (string) ($settings['cookie_privacy_url'] ?? ''),
            'web_vitals_enabled' => ! empty($settings['web_vitals_enabled']),
        ];
    }
}
