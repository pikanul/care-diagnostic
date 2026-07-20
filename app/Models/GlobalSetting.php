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
            'address_en' => 'Bhaluka, Mymensingh, Bangladesh',
            'address_bn' => 'ভালুকা, ময়মনসিংহ, বাংলাদেশ',
            'phone_primary' => '+880 1777-XXX XXX',
            'phone_secondary' => '+880 1777-XXX XXX',
            'emergency_number' => '+880 1777-XXX XXX',
            'email' => 'care.diagnostic.center@gmail.com',
            'opening_hours_en' => 'Mon - Sat: 8:00 AM - 10:00 PM',
            'opening_hours_bn' => 'সোম - শনি: সকাল ৮:০০ - রাত ১০:০০',
            'social_links' => [
                'facebook' => '#',
                'youtube' => '#',
                'instagram' => '#',
                'whatsapp' => '#',
            ],
            'whatsapp_link' => '#',
            'google_map_embed' => null,
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
