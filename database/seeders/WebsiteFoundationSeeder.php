<?php

namespace Database\Seeders;

use App\Models\FooterLink;
use App\Models\FooterSection;
use App\Models\HomepageSection;
use App\Models\GlobalSetting;
use App\Models\NavigationItem;
use App\Support\HomepageBuilder;
use Illuminate\Database\Seeder;

class WebsiteFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $settings = GlobalSetting::query()->first();
        $defaults = GlobalSetting::defaults();

        if (! $settings) {
            GlobalSetting::create($defaults);
        } else {
            $settings->update($defaults);
        }

        NavigationItem::query()->where('location', 'header')->update(['is_active' => false]);

        $navigationItems = [
            [
                'label_en' => 'Home',
                'label_bn' => 'হোম',
                'url' => '/en',
                'location' => 'header',
                'display_order' => 0,
                'is_active' => true,
            ],
            [
                'label_en' => 'About Us',
                'label_bn' => 'আমাদের সম্পর্কে',
                'url' => '/en#why-choose-us',
                'location' => 'header',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'label_en' => 'Services',
                'label_bn' => 'সেবা সমূহ',
                'url' => '/en#main-services',
                'location' => 'header',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'label_en' => 'Doctors',
                'label_bn' => 'চিকিৎসক সমূহ',
                'url' => '/en#specialist-doctors',
                'location' => 'header',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'label_en' => 'Tests',
                'label_bn' => 'টেস্ট সমূহ',
                'url' => '/en#diagnostic-test-categories',
                'location' => 'header',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'label_en' => 'Facilities',
                'label_bn' => 'সুবিধা সমূহ',
                'url' => '/en#gallery',
                'location' => 'header',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'label_en' => 'Gallery',
                'label_bn' => 'গ্যালারি',
                'url' => '/en#facility-showcase',
                'location' => 'header',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'label_en' => 'Blog',
                'label_bn' => 'ব্লগ',
                'url' => '/en#latest-articles',
                'location' => 'header',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'label_en' => 'Contact Us',
                'label_bn' => 'যোগাযোগ',
                'url' => '/en#appointment-cta',
                'location' => 'header',
                'display_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($navigationItems as $item) {
            $navigationItem = NavigationItem::withTrashed()->updateOrCreate(
                ['location' => 'header', 'label_en' => $item['label_en'], 'parent_id' => null],
                $item + ['open_in_new_tab' => false]
            );

            if ($navigationItem->trashed()) {
                $navigationItem->restore();
            }
        }

        FooterSection::query()->update(['is_active' => false]);
        FooterLink::query()->update(['is_active' => false]);

        $footerSections = [
            'quick-links' => [
                'title_en' => 'Quick Links',
                'title_bn' => 'দ্রুত লিংক',
                'display_order' => 0,
                'is_active' => true,
                'links' => [
                    ['label_en' => 'Home', 'label_bn' => 'হোম', 'url' => '/en', 'display_order' => 0],
                    ['label_en' => 'About Us', 'label_bn' => 'আমাদের সম্পর্কে', 'url' => '/en#why-choose-us', 'display_order' => 1],
                    ['label_en' => 'Doctors', 'label_bn' => 'চিকিৎসক সমূহ', 'url' => '/en#specialist-doctors', 'display_order' => 2],
                    ['label_en' => 'Tests', 'label_bn' => 'টেস্ট সমূহ', 'url' => '/en#diagnostic-test-categories', 'display_order' => 3],
                    ['label_en' => 'Contact', 'label_bn' => 'যোগাযোগ', 'url' => '/en#appointment-cta', 'display_order' => 4],
                ],
            ],
            'services' => [
                'title_en' => 'Services',
                'title_bn' => 'সেবা সমূহ',
                'display_order' => 1,
                'is_active' => true,
                'links' => [
                    ['label_en' => 'Diagnostics & Lab', 'label_bn' => 'ডায়াগনস্টিক ও ল্যাব', 'url' => '/en#main-services', 'display_order' => 0],
                    ['label_en' => 'Physiotherapy & Rehab', 'label_bn' => 'ফিজিওথেরাপি ও রিহ্যাব', 'url' => '/en#physiotherapy-services', 'display_order' => 1],
                    ['label_en' => 'Surgery & OT Services', 'label_bn' => 'সার্জারি ও ওটি সেবা', 'url' => '/en#main-services', 'display_order' => 2],
                    ['label_en' => 'Home Sample Collection', 'label_bn' => 'হোম স্যাম্পল কালেকশন', 'url' => '/en#home-sample-collection', 'display_order' => 3],
                    ['label_en' => 'Emergency Care 24/7', 'label_bn' => 'জরুরি সেবা ২৪/৭', 'url' => '/en#appointment-cta', 'display_order' => 4],
                ],
            ],
            'contact-info' => [
                'title_en' => 'Contact Info',
                'title_bn' => 'যোগাযোগের তথ্য',
                'display_order' => 2,
                'is_active' => true,
                'links' => [
                    ['label_en' => 'Katiadi, Kishoreganj', 'label_bn' => 'কটিয়াদী, কিশোরগঞ্জ', 'url' => '/en#appointment-cta', 'display_order' => 0],
                    ['label_en' => '+880 1777-XXX XXX', 'label_bn' => '+৮৮০ ১৭৭৭-XXX XXX', 'url' => 'tel:+8801777XXXXXX', 'display_order' => 1],
                    ['label_en' => 'care.diagnostic.center@gmail.com', 'label_bn' => 'care.diagnostic.center@gmail.com', 'url' => 'mailto:care.diagnostic.center@gmail.com', 'display_order' => 2],
                ],
            ],
            'newsletter' => [
                'title_en' => 'Newsletter',
                'title_bn' => 'নিউজলেটার',
                'display_order' => 3,
                'is_active' => true,
                'links' => [
                    ['label_en' => 'Get health tips and updates from Care Diagnostic.', 'label_bn' => 'স্বাস্থ্য টিপস ও আপডেট পেতে আমাদের সাথে থাকুন।', 'url' => '/en#appointment-cta', 'display_order' => 0],
                ],
            ],
        ];

        foreach ($footerSections as $sectionData) {
            $links = $sectionData['links'];
            unset($sectionData['links']);

            $footerSection = FooterSection::withTrashed()->updateOrCreate(
                ['title_en' => $sectionData['title_en']],
                $sectionData
            );

            if ($footerSection->trashed()) {
                $footerSection->restore();
            }

            foreach ($links as $link) {
                $footerLink = FooterLink::withTrashed()->updateOrCreate(
                    ['footer_section_id' => $footerSection->id, 'label_en' => $link['label_en']],
                    $link + [
                        'footer_section_id' => $footerSection->id,
                        'is_active' => true,
                        'open_in_new_tab' => false,
                    ]
                );

                if ($footerLink->trashed()) {
                    $footerLink->restore();
                }
            }
        }

        foreach (HomepageBuilder::sections() as $key => $definition) {
            $displayLimits = [
                'hero-slider' => 3,
                'trust-highlights' => 4,
                'quick-action-panel' => 4,
                'main-services' => 7,
                'quality-highlight-strip' => 6,
                'diagnostic-test-categories' => 5,
                'specialist-doctors' => 6,
                'statistics' => 4,
                'facility-showcase' => 6,
                'physiotherapy-services' => 16,
            ];

            $section = HomepageSection::firstOrCreate(
                ['section_key' => $key],
                [
                    'section_label_en' => $definition['label_en'],
                    'section_label_bn' => $definition['label_bn'],
                    'section_type' => $definition['type'],
                    'display_order' => array_search($key, array_keys(HomepageBuilder::sections()), true) ?: 0,
                    'is_active' => true,
                    'preview_enabled' => true,
                    'auto_scroll' => $key === 'hero-slider',
                    'carousel_speed' => $key === 'hero-slider' ? 4500 : 4000,
                    'display_limit' => $displayLimits[$key] ?? 4,
                    'background_color' => in_array($key, ['quality-highlight-strip', 'appointment-cta'], true) ? '#eef6f5' : null,
                ]
            );

            $defaults = HomepageBuilder::defaultContent($key);

            $section->update([
                'section_label_en' => $definition['label_en'],
                'section_label_bn' => $definition['label_bn'],
                'section_type' => $definition['type'],
                'title_en' => $defaults['title_en'] ?? $section->title_en ?: $definition['label_en'],
                'title_bn' => $defaults['title_bn'] ?? $section->title_bn ?: $definition['label_bn'],
                'subtitle_en' => $defaults['subtitle_en'] ?? $section->subtitle_en,
                'subtitle_bn' => $defaults['subtitle_bn'] ?? $section->subtitle_bn,
                'summary_en' => $defaults['summary_en'] ?? $section->summary_en,
                'summary_bn' => $defaults['summary_bn'] ?? $section->summary_bn,
                'content_en' => $defaults['content_en'] ?? $section->content_en,
                'content_bn' => $defaults['content_bn'] ?? $section->content_bn,
                'section_data' => $defaults ?: $section->section_data,
                'display_limit' => $displayLimits[$key] ?? $section->display_limit,
                'auto_scroll' => in_array($key, ['hero-slider', 'specialist-doctors'], true),
                'carousel_speed' => $key === 'hero-slider' ? 4500 : 4000,
            ]);
        }
    }
}
