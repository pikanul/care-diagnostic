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
            'whatsapp_link' => ['nullable', 'url'],
            'google_map_embed' => ['nullable', 'string'],
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
        ]);

        foreach (['logo_path', 'logo_mobile_path', 'favicon_path'] as $field) {
            if ($request->hasFile($field)) {
                if ($setting->{$field}) {
                    Storage::disk('public')->delete($setting->{$field});
                }

                $validated[$field] = $request->file($field)->store('site-settings', 'public');
            } else {
                unset($validated[$field]);
            }
        }

        $validated['contact_buttons_visible'] = $request->boolean('contact_buttons_visible');
        $validated['header_top_bar_visible'] = $request->boolean('header_top_bar_visible');
        $validated['newsletter_visible'] = $request->boolean('newsletter_visible');
        $validated['social_links'] = $validated['social_links'] ?? null;

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
}
