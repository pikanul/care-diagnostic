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

        .social-row {
            align-items: end;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px;
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
        }
    </style>

    <form class="form-grid" method="POST" action="{{ route('admin.global-settings.update') }}" enctype="multipart/form-data">
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

    <script>
        document.querySelector('[data-add-social-link]')?.addEventListener('click', () => {
            const list = document.querySelector('[data-social-link-list]');
            const template = document.querySelector('[data-social-link-template]');

            if (!list || !template) {
                return;
            }

            const index = list.querySelectorAll('[data-social-link-row]').length;
            const fragment = template.content.cloneNode(true);

            fragment.querySelectorAll('[data-name]').forEach((input) => {
                input.name = input.dataset.name.replace('__INDEX__', index);
                input.removeAttribute('data-name');
            });

            list.appendChild(fragment);
        });
    </script>
@endsection
