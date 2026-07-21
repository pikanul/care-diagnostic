@extends('layouts.admin', ['title' => 'Global Settings'])

@section('content')
    @php
        $socialRows = old('social_links_dynamic');

        if (! is_array($socialRows)) {
            $socialRows = collect($setting->social_links ?? [])
                ->map(fn ($url, $platform) => ['platform' => $platform, 'url' => $url])
                ->values()
                ->all();
        }

        if ($socialRows === []) {
            $socialRows = [
                ['platform' => 'facebook', 'url' => ''],
                ['platform' => 'instagram', 'url' => ''],
                ['platform' => 'youtube', 'url' => ''],
                ['platform' => 'whatsapp', 'url' => $setting->whatsapp_link ?? ''],
            ];
        }

        $seoSettings = array_merge(\App\Models\GlobalSetting::defaults()['seo_settings'] ?? [], old('seo_settings', $setting->seo_settings ?? []));
        $marketingRows = old('marketing_tools', data_get($setting->marketing_tools, 'tools', []));

        if (! is_array($marketingRows) || $marketingRows === []) {
            $marketingRows = [
                ['name' => 'Google Analytics 4', 'provider' => 'google_analytics_4', 'tracking_id' => '', 'script_head' => '', 'script_body' => '', 'is_active' => false],
                ['name' => 'Google Tag Manager', 'provider' => 'google_tag_manager', 'tracking_id' => '', 'script_head' => '', 'script_body' => '', 'is_active' => false],
                ['name' => 'Meta Pixel', 'provider' => 'meta_pixel', 'tracking_id' => '', 'script_head' => '', 'script_body' => '', 'is_active' => false],
            ];
        }

        $smsSettings = array_merge(\App\Models\GlobalSetting::defaults()['sms_settings'] ?? [], old('sms_settings', $setting->sms_settings ?? []));
        $emailIntegrationSettings = array_merge(\App\Models\GlobalSetting::defaults()['email_integration_settings'] ?? [], old('email_integration_settings', $setting->email_integration_settings ?? []));
        $visitorTrackingSettings = array_merge(\App\Models\GlobalSetting::defaults()['visitor_tracking_settings'] ?? [], old('visitor_tracking_settings', $setting->visitor_tracking_settings ?? []));
    @endphp

    <style>
        .asset-actions,
        .settings-section-heading,
        .social-row {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .settings-section-heading {
            justify-content: space-between;
            margin: 4px 0 10px;
        }

        .settings-section-heading h3 {
            margin: 0;
            font-size: 16px;
        }

        .social-list {
            display: grid;
            gap: 12px;
        }

        .wide-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .full-field {
            grid-column: 1 / -1;
        }

        .social-row {
            align-items: end;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px;
        }

        .marketing-row {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            align-items: start;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 14px;
        }

        .social-row label {
            flex: 1 1 180px;
        }

        .button.secondary {
            background: #e7eef8;
            color: #12325f;
        }

        .check {
            align-items: center;
            display: flex;
            gap: 8px;
        }

        .check input {
            width: auto;
        }

        @media (max-width: 680px) {
            .asset-actions,
            .settings-section-heading,
            .social-row {
                align-items: stretch;
                flex-direction: column;
            }

            .wide-grid,
            .marketing-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <form class="form-grid form-wide" method="POST" action="{{ route('admin.global-settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="panel">
            <h2>Hospital Identity</h2>
            <label>Hospital Name (English)
                <input type="text" name="hospital_name_en" value="{{ old('hospital_name_en', $setting->hospital_name_en) }}" required>
            </label>
            <label>Hospital Name (Bangla)
                <input type="text" name="hospital_name_bn" value="{{ old('hospital_name_bn', $setting->hospital_name_bn) }}" required>
            </label>
        </div>

        <div class="panel">
            <h2>Brand Assets</h2>
            <label>Logo
                <input type="file" name="logo_path" accept="image/*">
            </label>
            @if ($setting->logo_path)
                <div class="asset-actions">
                    <p class="muted">Current logo: <a href="{{ asset('storage/'.$setting->logo_path) }}" target="_blank">view</a></p>
                    <label class="check"><input type="checkbox" name="remove_logo_path" value="1"> Remove current logo</label>
                </div>
            @endif

            <label>Mobile Logo
                <input type="file" name="logo_mobile_path" accept="image/*">
            </label>
            @if ($setting->logo_mobile_path)
                <div class="asset-actions">
                    <p class="muted">Current mobile logo: <a href="{{ asset('storage/'.$setting->logo_mobile_path) }}" target="_blank">view</a></p>
                    <label class="check"><input type="checkbox" name="remove_logo_mobile_path" value="1"> Remove current mobile logo</label>
                </div>
            @endif

            <label>Favicon
                <input type="file" name="favicon_path" accept="image/*,.ico">
            </label>
            @if ($setting->favicon_path)
                <div class="asset-actions">
                    <p class="muted">Current favicon: <a href="{{ asset('storage/'.$setting->favicon_path) }}" target="_blank">view</a></p>
                    <label class="check"><input type="checkbox" name="remove_favicon_path" value="1"> Remove current favicon</label>
                </div>
            @endif
        </div>

        <div class="panel">
            <h2>Contact Details</h2>
            <label>Address (English)
                <textarea name="address_en" rows="3">{{ old('address_en', $setting->address_en) }}</textarea>
            </label>
            <label>Address (Bangla)
                <textarea name="address_bn" rows="3">{{ old('address_bn', $setting->address_bn) }}</textarea>
            </label>
            <label>Primary Phone
                <input type="text" name="phone_primary" value="{{ old('phone_primary', $setting->phone_primary) }}">
            </label>
            <label>Secondary Phone
                <input type="text" name="phone_secondary" value="{{ old('phone_secondary', $setting->phone_secondary) }}">
            </label>
            <label>Emergency Number
                <input type="text" name="emergency_number" value="{{ old('emergency_number', $setting->emergency_number) }}">
            </label>
            <label>Email
                <input type="email" name="email" value="{{ old('email', $setting->email) }}">
            </label>
            <label>Opening Hours (English)
                <textarea name="opening_hours_en" rows="2">{{ old('opening_hours_en', $setting->opening_hours_en) }}</textarea>
            </label>
            <label>Opening Hours (Bangla)
                <textarea name="opening_hours_bn" rows="2">{{ old('opening_hours_bn', $setting->opening_hours_bn) }}</textarea>
            </label>
        </div>

        <div class="panel">
            <h2>Buttons and Visibility</h2>
            <label class="check"><input type="checkbox" name="contact_buttons_visible" value="1" @checked(old('contact_buttons_visible', $setting->contact_buttons_visible))> Contact buttons visible</label>
            <label class="check"><input type="checkbox" name="header_top_bar_visible" value="1" @checked(old('header_top_bar_visible', $setting->header_top_bar_visible))> Header top bar visible</label>
            <label class="check"><input type="checkbox" name="newsletter_visible" value="1" @checked(old('newsletter_visible', $setting->newsletter_visible))> Newsletter visible</label>

            <label>Default Language
                <select name="default_language">
                    <option value="en" @selected(old('default_language', $setting->default_language) === 'en')>English</option>
                    <option value="bn" @selected(old('default_language', $setting->default_language) === 'bn')>Bangla</option>
                </select>
            </label>

            <label>Appointment Button Label (English)
                <input type="text" name="book_appointment_button_label_en" value="{{ old('book_appointment_button_label_en', $setting->book_appointment_button_label_en) }}">
            </label>
            <label>Appointment Button Label (Bangla)
                <input type="text" name="book_appointment_button_label_bn" value="{{ old('book_appointment_button_label_bn', $setting->book_appointment_button_label_bn) }}">
            </label>
            <label>Appointment Button URL
                <input type="text" name="book_appointment_button_url" value="{{ old('book_appointment_button_url', $setting->book_appointment_button_url) }}">
            </label>
            <label>Emergency Button Label (English)
                <input type="text" name="emergency_button_label_en" value="{{ old('emergency_button_label_en', $setting->emergency_button_label_en) }}">
            </label>
            <label>Emergency Button Label (Bangla)
                <input type="text" name="emergency_button_label_bn" value="{{ old('emergency_button_label_bn', $setting->emergency_button_label_bn) }}">
            </label>
            <label>Emergency Button URL
                <input type="text" name="emergency_button_url" value="{{ old('emergency_button_url', $setting->emergency_button_url) }}">
            </label>
        </div>

        <div class="panel">
            <h2>Footer and Social</h2>
            <label>Footer Description (English)
                <textarea name="footer_description_en" rows="3">{{ old('footer_description_en', $setting->footer_description_en) }}</textarea>
            </label>
            <label>Footer Description (Bangla)
                <textarea name="footer_description_bn" rows="3">{{ old('footer_description_bn', $setting->footer_description_bn) }}</textarea>
            </label>
            <label>Copyright Text (English)
                <textarea name="copyright_text_en" rows="2">{{ old('copyright_text_en', $setting->copyright_text_en) }}</textarea>
            </label>
            <label>Copyright Text (Bangla)
                <textarea name="copyright_text_bn" rows="2">{{ old('copyright_text_bn', $setting->copyright_text_bn) }}</textarea>
            </label>
            <label>WhatsApp Link
                <input type="text" name="whatsapp_link" value="{{ old('whatsapp_link', $setting->whatsapp_link) }}">
            </label>
            <label>Google Map Embed
                <textarea name="google_map_embed" rows="3">{{ old('google_map_embed', $setting->google_map_embed) }}</textarea>
            </label>
            <div>
                <div class="settings-section-heading">
                    <h3>Social Links</h3>
                    <button class="button secondary" type="button" data-add-social-link>Add Social Link</button>
                </div>

                <div class="social-list" data-social-link-list>
                    @foreach ($socialRows as $index => $row)
                        <div class="social-row" data-social-link-row>
                            <label>Platform Key
                                <input type="text" name="social_links_dynamic[{{ $index }}][platform]" value="{{ data_get($row, 'platform') }}" placeholder="facebook">
                            </label>
                            <label>URL
                                <input type="url" name="social_links_dynamic[{{ $index }}][url]" value="{{ data_get($row, 'url') }}" placeholder="https://example.com">
                            </label>
                            <label class="check"><input type="checkbox" name="social_links_dynamic[{{ $index }}][remove]" value="1" @checked(data_get($row, 'remove'))> Delete</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="panel">
            <h2>SEO Settings</h2>
            <div class="wide-grid">
                <label>Meta Title (English)
                    <input type="text" name="seo_settings[meta_title_en]" value="{{ data_get($seoSettings, 'meta_title_en') }}">
                </label>
                <label>Meta Title (Bangla)
                    <input type="text" name="seo_settings[meta_title_bn]" value="{{ data_get($seoSettings, 'meta_title_bn') }}">
                </label>
                <label>Meta Description (English)
                    <textarea name="seo_settings[meta_description_en]" rows="3">{{ data_get($seoSettings, 'meta_description_en') }}</textarea>
                </label>
                <label>Meta Description (Bangla)
                    <textarea name="seo_settings[meta_description_bn]" rows="3">{{ data_get($seoSettings, 'meta_description_bn') }}</textarea>
                </label>
                <label>Meta Keywords (English)
                    <textarea name="seo_settings[meta_keywords_en]" rows="2">{{ data_get($seoSettings, 'meta_keywords_en') }}</textarea>
                </label>
                <label>Meta Keywords (Bangla)
                    <textarea name="seo_settings[meta_keywords_bn]" rows="2">{{ data_get($seoSettings, 'meta_keywords_bn') }}</textarea>
                </label>
                <label>Canonical URL
                    <input type="url" name="seo_settings[canonical_url]" value="{{ data_get($seoSettings, 'canonical_url') }}">
                </label>
                <label>Robots
                    <input type="text" name="seo_settings[robots]" value="{{ data_get($seoSettings, 'robots') }}" placeholder="index,follow">
                </label>
                <label>Google Site Verification
                    <input type="text" name="seo_settings[google_site_verification]" value="{{ data_get($seoSettings, 'google_site_verification') }}">
                </label>
                <label>Bing Site Verification
                    <input type="text" name="seo_settings[bing_site_verification]" value="{{ data_get($seoSettings, 'bing_site_verification') }}">
                </label>
                <label>Facebook Domain Verification
                    <input type="text" name="seo_settings[facebook_domain_verification]" value="{{ data_get($seoSettings, 'facebook_domain_verification') }}">
                </label>
                <label>Open Graph / Social Share Image
                    <input type="file" name="seo_og_image_file" accept="image/*">
                </label>
            </div>
            @if (data_get($seoSettings, 'og_image_path'))
                <div class="asset-actions">
                    <p class="muted">Current social share image: <a href="{{ asset('storage/'.data_get($seoSettings, 'og_image_path')) }}" target="_blank">view</a></p>
                    <label class="check"><input type="checkbox" name="remove_seo_og_image" value="1"> Remove current social share image</label>
                </div>
            @endif
        </div>

        <div class="panel">
            <div class="settings-section-heading">
                <h2 style="margin:0;">Digital Marketing Tools</h2>
                <button class="button secondary" type="button" data-add-marketing-tool>Add Marketing Tool</button>
            </div>
            <p class="muted">Add Google Analytics, Tag Manager, Meta Pixel, Hotjar, Google Ads, TikTok Pixel, LinkedIn Insight, or custom scripts here.</p>

            <div class="social-list" data-marketing-tool-list>
                @foreach ($marketingRows as $index => $row)
                    <div class="marketing-row" data-marketing-tool-row>
                        <label>Tool Name
                            <input type="text" name="marketing_tools[{{ $index }}][name]" value="{{ data_get($row, 'name') }}" placeholder="Google Analytics 4">
                        </label>
                        <label>Provider
                            <input type="text" name="marketing_tools[{{ $index }}][provider]" value="{{ data_get($row, 'provider') }}" placeholder="google_analytics_4">
                        </label>
                        <label>Tracking ID
                            <input type="text" name="marketing_tools[{{ $index }}][tracking_id]" value="{{ data_get($row, 'tracking_id') }}" placeholder="G-XXXXXXXXXX">
                        </label>
                        <label class="full-field">Head Script
                            <textarea name="marketing_tools[{{ $index }}][script_head]" rows="4" placeholder="Paste script for the HTML head">{{ data_get($row, 'script_head') }}</textarea>
                        </label>
                        <label class="full-field">Body Script
                            <textarea name="marketing_tools[{{ $index }}][script_body]" rows="4" placeholder="Paste noscript/body script if needed">{{ data_get($row, 'script_body') }}</textarea>
                        </label>
                        <label class="check"><input type="checkbox" name="marketing_tools[{{ $index }}][is_active]" value="1" @checked(data_get($row, 'is_active'))> Active</label>
                        <label class="check"><input type="checkbox" name="marketing_tools[{{ $index }}][remove]" value="1" @checked(data_get($row, 'remove'))> Delete</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <h2>SMS Integration</h2>
            <div class="wide-grid">
                <label class="check full-field"><input type="checkbox" name="sms_settings[enabled]" value="1" @checked(data_get($smsSettings, 'enabled'))> SMS integration enabled</label>
                <label>Provider
                    <input type="text" name="sms_settings[provider]" value="{{ data_get($smsSettings, 'provider') }}" placeholder="BD Bulk SMS / Twilio / Custom">
                </label>
                <label>Sender ID
                    <input type="text" name="sms_settings[sender_id]" value="{{ data_get($smsSettings, 'sender_id') }}">
                </label>
                <label>API Base URL
                    <input type="url" name="sms_settings[api_base_url]" value="{{ data_get($smsSettings, 'api_base_url') }}">
                </label>
                <label>API Key
                    <input type="text" name="sms_settings[api_key]" value="{{ data_get($smsSettings, 'api_key') }}">
                </label>
                <label>API Secret
                    <input type="password" name="sms_settings[api_secret]" value="{{ data_get($smsSettings, 'api_secret') }}">
                </label>
                <label>Test Number
                    <input type="text" name="sms_settings[test_number]" value="{{ data_get($smsSettings, 'test_number') }}">
                </label>
            </div>
        </div>

        <div class="panel">
            <h2>Email Integration</h2>
            <div class="wide-grid">
                <label class="check full-field"><input type="checkbox" name="email_integration_settings[enabled]" value="1" @checked(data_get($emailIntegrationSettings, 'enabled'))> Email integration enabled</label>
                <label>Provider
                    <input type="text" name="email_integration_settings[provider]" value="{{ data_get($emailIntegrationSettings, 'provider') }}" placeholder="smtp / mailgun / sendgrid / ses">
                </label>
                <label>From Name
                    <input type="text" name="email_integration_settings[from_name]" value="{{ data_get($emailIntegrationSettings, 'from_name') }}">
                </label>
                <label>From Email
                    <input type="email" name="email_integration_settings[from_email]" value="{{ data_get($emailIntegrationSettings, 'from_email') }}">
                </label>
                <label>Reply-To Email
                    <input type="email" name="email_integration_settings[reply_to]" value="{{ data_get($emailIntegrationSettings, 'reply_to') }}">
                </label>
                <label>SMTP Host
                    <input type="text" name="email_integration_settings[host]" value="{{ data_get($emailIntegrationSettings, 'host') }}">
                </label>
                <label>SMTP Port
                    <input type="number" name="email_integration_settings[port]" value="{{ data_get($emailIntegrationSettings, 'port') }}">
                </label>
                <label>Encryption
                    <input type="text" name="email_integration_settings[encryption]" value="{{ data_get($emailIntegrationSettings, 'encryption') }}" placeholder="tls / ssl">
                </label>
                <label>Username
                    <input type="text" name="email_integration_settings[username]" value="{{ data_get($emailIntegrationSettings, 'username') }}">
                </label>
                <label>Password
                    <input type="password" name="email_integration_settings[password]" value="{{ data_get($emailIntegrationSettings, 'password') }}">
                </label>
                <label>API Key
                    <input type="text" name="email_integration_settings[api_key]" value="{{ data_get($emailIntegrationSettings, 'api_key') }}">
                </label>
            </div>
        </div>

        <div class="panel">
            <h2>Visitor Tracking</h2>
            <div class="wide-grid">
                <label class="check"><input type="checkbox" name="visitor_tracking_settings[enabled]" value="1" @checked(data_get($visitorTrackingSettings, 'enabled'))> Track website visitors</label>
                <label class="check"><input type="checkbox" name="visitor_tracking_settings[anonymize_ip]" value="1" @checked(data_get($visitorTrackingSettings, 'anonymize_ip'))> Anonymize IP in future reports</label>
                <label>Real-Time Active Window (minutes)
                    <input type="number" name="visitor_tracking_settings[active_window_minutes]" value="{{ data_get($visitorTrackingSettings, 'active_window_minutes') }}" min="1" max="120">
                </label>
                <label>Retain Logs (days)
                    <input type="number" name="visitor_tracking_settings[retain_days]" value="{{ data_get($visitorTrackingSettings, 'retain_days') }}" min="1" max="3650">
                </label>
            </div>
        </div>

        <button type="submit">Save Settings</button>
    </form>

    <template data-social-link-template>
        <div class="social-row" data-social-link-row>
            <label>Platform Key
                <input type="text" data-name="social_links_dynamic[__INDEX__][platform]" placeholder="facebook">
            </label>
            <label>URL
                <input type="url" data-name="social_links_dynamic[__INDEX__][url]" placeholder="https://example.com">
            </label>
            <label class="check"><input type="checkbox" data-name="social_links_dynamic[__INDEX__][remove]" value="1"> Delete</label>
        </div>
    </template>

    <template data-marketing-tool-template>
        <div class="marketing-row" data-marketing-tool-row>
            <label>Tool Name
                <input type="text" data-name="marketing_tools[__INDEX__][name]" placeholder="Google Analytics 4">
            </label>
            <label>Provider
                <input type="text" data-name="marketing_tools[__INDEX__][provider]" placeholder="google_analytics_4">
            </label>
            <label>Tracking ID
                <input type="text" data-name="marketing_tools[__INDEX__][tracking_id]" placeholder="G-XXXXXXXXXX">
            </label>
            <label class="full-field">Head Script
                <textarea data-name="marketing_tools[__INDEX__][script_head]" rows="4" placeholder="Paste script for the HTML head"></textarea>
            </label>
            <label class="full-field">Body Script
                <textarea data-name="marketing_tools[__INDEX__][script_body]" rows="4" placeholder="Paste noscript/body script if needed"></textarea>
            </label>
            <label class="check"><input type="checkbox" data-name="marketing_tools[__INDEX__][is_active]" value="1"> Active</label>
            <label class="check"><input type="checkbox" data-name="marketing_tools[__INDEX__][remove]" value="1"> Delete</label>
        </div>
    </template>

    <script>
        const addRepeatableRow = (buttonSelector, listSelector, templateSelector, rowSelector) => {
            document.querySelector(buttonSelector)?.addEventListener('click', () => {
                const list = document.querySelector(listSelector);
                const template = document.querySelector(templateSelector);

                if (!list || !template) {
                    return;
                }

                const index = list.querySelectorAll(rowSelector).length;
                const fragment = template.content.cloneNode(true);

                fragment.querySelectorAll('[data-name]').forEach((input) => {
                    input.name = input.dataset.name.replace('__INDEX__', index);
                    input.removeAttribute('data-name');
                });

                list.appendChild(fragment);
            });
        };

        addRepeatableRow('[data-add-social-link]', '[data-social-link-list]', '[data-social-link-template]', '[data-social-link-row]');
        addRepeatableRow('[data-add-marketing-tool]', '[data-marketing-tool-list]', '[data-marketing-tool-template]', '[data-marketing-tool-row]');
    </script>
@endsection
