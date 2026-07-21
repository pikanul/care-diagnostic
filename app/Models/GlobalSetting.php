<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $fillable = [
        'hospital_name_en',
        'hospital_name_bn',
        'logo_path',
        'logo_mobile_path',
        'favicon_path',
        'address_en',
        'address_bn',
        'phone_primary',
        'phone_secondary',
        'emergency_number',
        'email',
        'opening_hours_en',
        'opening_hours_bn',
        'social_links',
        'whatsapp_link',
        'google_map_embed',
        'seo_settings',
        'marketing_tools',
        'sms_settings',
        'email_integration_settings',
        'visitor_tracking_settings',
        'default_language',
        'contact_buttons_visible',
        'header_top_bar_visible',
        'newsletter_visible',
        'book_appointment_button_label_en',
        'book_appointment_button_label_bn',
        'book_appointment_button_url',
        'emergency_button_label_en',
        'emergency_button_label_bn',
        'emergency_button_url',
        'footer_description_en',
        'footer_description_bn',
        'copyright_text_en',
        'copyright_text_bn',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'seo_settings' => 'array',
            'marketing_tools' => 'array',
            'sms_settings' => 'array',
            'email_integration_settings' => 'array',
            'visitor_tracking_settings' => 'array',
            'contact_buttons_visible' => 'boolean',
            'header_top_bar_visible' => 'boolean',
            'newsletter_visible' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::query()->first() ?? static::create(static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'hospital_name_en' => 'Care Diagnostic & Physiotherapy Center',
            'hospital_name_bn' => 'কেয়ার ডায়াগনস্টিক ও ফিজিওথেরাপি সেন্টার',
            'logo_path' => 'homepage/reference/logo.png',
            'logo_mobile_path' => 'homepage/reference/logo.png',
            'favicon_path' => 'homepage/reference/logo.png',
            'address_en' => 'Katiadi, Kishoreganj',
            'address_bn' => 'কটিয়াদী, কিশোরগঞ্জ',
            'phone_primary' => '+880 1777-XXX XXX',
            'phone_secondary' => '+880 1777-XXX XXX',
            'emergency_number' => '+880 1777-XXX XXX',
            'email' => 'care.diagnostic.center@gmail.com',
            'opening_hours_en' => null,
            'opening_hours_bn' => null,
            'social_links' => [
                'facebook' => '#',
                'youtube' => '#',
                'instagram' => '#',
                'whatsapp' => '#',
            ],
            'whatsapp_link' => '#',
            'google_map_embed' => null,
            'seo_settings' => [
                'meta_title_en' => 'Care Diagnostic & Physiotherapy Center',
                'meta_title_bn' => 'কেয়ার ডায়াগনস্টিক ও ফিজিওথেরাপি সেন্টার',
                'meta_description_en' => 'Care Diagnostic & Physiotherapy Center provides diagnostic, physiotherapy and patient-first healthcare support.',
                'meta_description_bn' => 'কেয়ার ডায়াগনস্টিক ও ফিজিওথেরাপি সেন্টার ডায়াগনস্টিক, ফিজিওথেরাপি ও যত্নশীল স্বাস্থ্যসেবা প্রদান করে।',
                'meta_keywords_en' => 'diagnostic center, physiotherapy, hospital, Katiadi, Kishoreganj',
                'meta_keywords_bn' => 'ডায়াগনস্টিক সেন্টার, ফিজিওথেরাপি, হাসপাতাল, কটিয়াদী, কিশোরগঞ্জ',
                'canonical_url' => null,
                'robots' => 'index,follow',
                'google_site_verification' => null,
                'bing_site_verification' => null,
                'facebook_domain_verification' => null,
                'og_image_path' => 'homepage/reference/hospital-theme-hero.png',
            ],
            'marketing_tools' => [
                'tools' => [],
            ],
            'sms_settings' => [
                'enabled' => false,
                'provider' => null,
                'sender_id' => null,
                'api_base_url' => null,
                'api_key' => null,
                'api_secret' => null,
                'test_number' => null,
            ],
            'email_integration_settings' => [
                'enabled' => false,
                'provider' => 'smtp',
                'from_name' => 'Care Diagnostic & Physiotherapy Center',
                'from_email' => 'care.diagnostic.center@gmail.com',
                'host' => null,
                'port' => null,
                'encryption' => null,
                'username' => null,
                'password' => null,
                'api_key' => null,
                'reply_to' => null,
            ],
            'visitor_tracking_settings' => [
                'enabled' => true,
                'anonymize_ip' => false,
                'active_window_minutes' => 5,
                'retain_days' => 365,
            ],
            'default_language' => 'en',
            'contact_buttons_visible' => true,
            'header_top_bar_visible' => true,
            'newsletter_visible' => true,
            'book_appointment_button_label_en' => 'Book Appointment',
            'book_appointment_button_label_bn' => 'অ্যাপয়েন্টমেন্ট নিন',
            'book_appointment_button_url' => '/en#appointment-cta',
            'emergency_button_label_en' => '24/7 Emergency Support',
            'emergency_button_label_bn' => '২৪/৭ জরুরি হেল্পলাইন',
            'emergency_button_url' => 'tel:+8801777XXXXXX',
            'footer_description_en' => 'Modern diagnostics, physiotherapy and compassionate healthcare support under one roof.',
            'footer_description_bn' => 'আধুনিক পরীক্ষা, ফিজিওথেরাপি ও যত্নশীল স্বাস্থ্যসেবা এক ছাদের নিচে।',
            'copyright_text_en' => '© '.date('Y').' Care Diagnostic & Physiotherapy Center. All rights reserved.',
            'copyright_text_bn' => '© '.date('Y').' কেয়ার ডায়াগনস্টিক ও ফিজিওথেরাপি সেন্টার। সর্বস্বত্ব সংরক্ষিত।',
        ];
    }
}
