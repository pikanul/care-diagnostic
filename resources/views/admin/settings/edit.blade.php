@extends('layouts.admin', ['title' => 'Global Settings'])

@section('content')
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
                <p class="muted">Current logo: <a href="{{ asset('storage/'.$setting->logo_path) }}" target="_blank">view</a></p>
            @endif

            <label>Mobile Logo
                <input type="file" name="logo_mobile_path" accept="image/*">
            </label>
            @if ($setting->logo_mobile_path)
                <p class="muted">Current mobile logo: <a href="{{ asset('storage/'.$setting->logo_mobile_path) }}" target="_blank">view</a></p>
            @endif

            <label>Favicon
                <input type="file" name="favicon_path" accept="image/*,.ico">
            </label>
            @if ($setting->favicon_path)
                <p class="muted">Current favicon: <a href="{{ asset('storage/'.$setting->favicon_path) }}" target="_blank">view</a></p>
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
            <label>Facebook
                <input type="url" name="social_links[facebook]" value="{{ old('social_links.facebook', data_get($setting->social_links, 'facebook')) }}">
            </label>
            <label>YouTube
                <input type="url" name="social_links[youtube]" value="{{ old('social_links.youtube', data_get($setting->social_links, 'youtube')) }}">
            </label>
            <label>Instagram
                <input type="url" name="social_links[instagram]" value="{{ old('social_links.instagram', data_get($setting->social_links, 'instagram')) }}">
            </label>
            <label>LinkedIn
                <input type="url" name="social_links[linkedin]" value="{{ old('social_links.linkedin', data_get($setting->social_links, 'linkedin')) }}">
            </label>
            <label>X / Twitter
                <input type="url" name="social_links[x]" value="{{ old('social_links.x', data_get($setting->social_links, 'x')) }}">
            </label>
        </div>

        <button type="submit">Save Settings</button>
    </form>
@endsection
