<?php

namespace App\Support;

class HomepageBuilder
{
    public static function sections(): array
    {
        return [
            'top-information-bar' => ['label_en' => 'Top Information Bar', 'label_bn' => 'টপ তথ্য বার', 'type' => 'layout'],
            'main-navigation' => ['label_en' => 'Main Navigation', 'label_bn' => 'মেইন ন্যাভিগেশন', 'type' => 'layout'],
            'hero-slider' => ['label_en' => 'Hero Image', 'label_bn' => 'হিরো ছবি', 'type' => 'slider'],
            'trust-highlights' => ['label_en' => 'Trust Highlights', 'label_bn' => 'বিশ্বাসের হাইলাইট', 'type' => 'cards'],
            'quick-action-panel' => ['label_en' => 'Quick Action Panel', 'label_bn' => 'দ্রুত অ্যাকশন প্যানেল', 'type' => 'actions'],
            'main-services' => ['label_en' => 'Our Services', 'label_bn' => 'আমাদের সেবা সমূহ', 'type' => 'cards'],
            'quality-highlight-strip' => ['label_en' => 'Quality Highlight Strip', 'label_bn' => 'গুণমান হাইলাইট স্ট্রিপ', 'type' => 'strip'],
            'diagnostic-test-categories' => ['label_en' => 'Accurate Tests, Reliable Results', 'label_bn' => 'নির্ভুল পরীক্ষা, নির্ভরযোগ্য রিপোর্ট', 'type' => 'cards'],
            'specialist-doctors' => ['label_en' => 'Specialist Doctors', 'label_bn' => 'বিশেষজ্ঞ ডাক্তার', 'type' => 'cards'],
            'why-choose-us' => ['label_en' => 'Why Choose Us', 'label_bn' => 'কেন আমাদের বেছে নেবেন', 'type' => 'points'],
            'statistics' => ['label_en' => 'Statistics', 'label_bn' => 'পরিসংখ্যান', 'type' => 'stats'],
            'facility-showcase' => ['label_en' => 'Facility Showcase', 'label_bn' => 'সুবিধা প্রদর্শনী', 'type' => 'cards'],
            'home-sample-collection' => ['label_en' => 'Home Sample Collection', 'label_bn' => 'হোম স্যাম্পল কালেকশন', 'type' => 'cta'],
            'physiotherapy-services' => ['label_en' => 'Physiotherapy Services', 'label_bn' => 'ফিজিওথেরাপি সেবা', 'type' => 'cards'],
            'health-packages' => ['label_en' => 'Health Packages', 'label_bn' => 'হেলথ প্যাকেজ', 'type' => 'cards'],
            'testimonials' => ['label_en' => 'Testimonials', 'label_bn' => 'টেস্টিমোনিয়াল', 'type' => 'cards'],
            'latest-articles' => ['label_en' => 'Latest Articles', 'label_bn' => 'সাম্প্রতিক আর্টিকেল', 'type' => 'cards'],
            'appointment-cta' => ['label_en' => 'Appointment CTA', 'label_bn' => 'অ্যাপয়েন্টমেন্ট কল-টু-অ্যাকশন', 'type' => 'cta'],
            'footer' => ['label_en' => 'Footer', 'label_bn' => 'ফুটার', 'type' => 'layout'],
        ];
    }

    public static function defaultContent(string $key): array
    {
        if ($key === 'hero-slider') {
            return [
                'title_en' => 'Welcome to Care Diagnostic & Physiotherapy Center',
                'title_bn' => 'কেয়ার ডায়াগনস্টিক ও ফিজিওথেরাপি সেন্টারে স্বাগতম',
                'settings' => [
                    'auto_play' => true,
                    'pause_on_hover' => true,
                    'swipe' => true,
                    'keyboard' => true,
                    'dots' => true,
                    'arrows' => true,
                    'lazy_load_after_first_slide' => true,
                    'overlay_opacity' => 60,
                    'text_alignment' => 'left',
                ],
                'slides' => [
                    [
                        'media_type' => 'image',
                        'display_order' => 1,
                        'is_active' => true,
                        'publish_at' => null,
                        'expires_at' => null,
                        'overlay_opacity' => 60,
                        'text_alignment' => 'left',
                        'title_en' => 'Compassionate Care, Advanced Treatment',
                        'title_bn' => 'উন্নত প্রযুক্তিতে নির্ভরযোগ্য ডায়াগনস্টিক ও ফিজিওথেরাপি সেবা',
                        'subtitle_en' => 'Care Diagnostic & Physiotherapy Center is committed to providing quality healthcare with modern technology, expert professionals and patient-first approach.',
                        'subtitle_bn' => 'কেয়ার ডায়াগনস্টিক ও ফিজিওথেরাপি সেন্টার আধুনিক প্রযুক্তি, দক্ষ পেশাদার এবং রোগী-কেন্দ্রিক সেবার মাধ্যমে মানসম্পন্ন স্বাস্থ্যসেবা দিতে প্রতিশ্রুতিবদ্ধ।',
                        'primary_cta_label_en' => 'Book Appointment',
                        'primary_cta_label_bn' => 'অ্যাপয়েন্টমেন্ট নিন',
                        'primary_cta_url' => '/en#appointment-cta',
                        'secondary_cta_label_en' => 'Our Services',
                        'secondary_cta_label_bn' => 'আমাদের সেবা দেখুন',
                        'secondary_cta_url' => '/en#main-services',
                        'desktop_image_path' => 'homepage/reference/hospital-theme-hero.png',
                        'mobile_image_path' => 'homepage/reference/hospital-theme-hero.png',
                    ],
                ],
            ];
        }

        if ($key === 'trust-highlights') {
            return [
                'title_en' => 'Trusted Healthcare for Every Family',
                'title_bn' => 'প্রতিটি পরিবারের জন্য বিশ্বস্ত স্বাস্থ্যসেবা',
                'items' => [
                    ['title_en' => 'Expert Doctors', 'title_bn' => 'অভিজ্ঞ ডাক্তার', 'description_en' => 'Experienced & caring', 'description_bn' => 'অভিজ্ঞ ও যত্নশীল'],
                    ['title_en' => 'Modern Equipment', 'title_bn' => 'আধুনিক যন্ত্রপাতি', 'description_en' => 'Advanced technology', 'description_bn' => 'উন্নত প্রযুক্তি'],
                    ['title_en' => 'Accurate Results', 'title_bn' => 'নির্ভুল ফলাফল', 'description_en' => 'Reliable reports', 'description_bn' => 'বিশ্বস্ত রিপোর্ট'],
                    ['title_en' => 'Affordable Care', 'title_bn' => 'সাশ্রয়ী সেবা', 'description_en' => 'Quality for everyone', 'description_bn' => 'সবার জন্য মানসম্মত'],
                ],
            ];
        }

        if ($key === 'quick-action-panel') {
            return [
                'title_en' => 'Quick Care Access',
                'title_bn' => 'দ্রুত সেবা নিন',
                'actions' => [
                    ['title_en' => '24/7', 'title_bn' => '২৪/৭', 'subtitle_en' => 'Emergency Care', 'subtitle_bn' => 'জরুরি সেবা', 'url' => 'tel:+8801777XXXXXX'],
                    ['title_en' => 'Home Sample', 'title_bn' => 'হোম স্যাম্পল', 'subtitle_en' => 'Collection', 'subtitle_bn' => 'কালেকশন', 'url' => '/en#home-sample-collection'],
                    ['title_en' => 'Quick', 'title_bn' => 'দ্রুত', 'subtitle_en' => 'Appointments', 'subtitle_bn' => 'অ্যাপয়েন্টমেন্ট', 'url' => '/en#appointment-cta'],
                    ['title_en' => 'Trusted by', 'title_bn' => 'বিশ্বাস', 'subtitle_en' => 'Thousands', 'subtitle_bn' => 'হাজারো মানুষের', 'url' => '/en#why-choose-us'],
                ],
            ];
        }

        if ($key === 'main-services') {
            return [
                'title_en' => 'Our Services',
                'title_bn' => 'আমাদের সেবা সমূহ',
                'items' => [
                    ['title_en' => 'Diagnostics & Laboratory', 'title_bn' => 'ডায়াগনস্টিক ও ল্যাবরেটরি', 'subtitle_en' => 'Advanced testing facilities with accurate reports', 'subtitle_bn' => 'নির্ভুল রিপোর্টসহ আধুনিক পরীক্ষার সুবিধা', 'accent' => '#1660d9', 'image_path' => 'homepage/reference/service-diagnostics.png'],
                    ['title_en' => 'Physiotherapy & Rehabilitation', 'title_bn' => 'ফিজিওথেরাপি ও রিহ্যাবিলিটেশন', 'subtitle_en' => 'Pain relief and rehabilitation by expert physiotherapists', 'subtitle_bn' => 'দক্ষ ফিজিওথেরাপিস্টদের মাধ্যমে ব্যথা উপশম ও পুনর্বাসন', 'accent' => '#12936b', 'image_path' => 'homepage/reference/service-physio.png'],
                    ['title_en' => 'Patient Care', 'title_bn' => 'রোগী সেবা', 'subtitle_en' => 'Personalized care for you and your loved ones', 'subtitle_bn' => 'আপনি ও আপনার প্রিয়জনের জন্য ব্যক্তিগত যত্ন', 'accent' => '#f05a7e', 'image_path' => 'homepage/reference/service-patient-care.png'],
                    ['title_en' => 'Home Sample Collection', 'title_bn' => 'হোম স্যাম্পল কালেকশন', 'subtitle_en' => 'Sample collection from the comfort of your home', 'subtitle_bn' => 'বাড়ির স্বাচ্ছন্দ্যে নমুনা সংগ্রহ', 'accent' => '#7c5cff', 'image_path' => 'homepage/reference/service-home-sample.png'],
                    ['title_en' => 'Surgery & OT Services', 'title_bn' => 'সার্জারি ও ওটি সেবা', 'subtitle_en' => 'Minor, major & emergency surgical care', 'subtitle_bn' => 'ছোট, বড় ও জরুরি অপারেশন সেবা', 'accent' => '#1356c7', 'image_path' => 'homepage/reference/service-surgery.png'],
                    ['title_en' => 'Women & Child Health', 'title_bn' => 'নারী ও শিশু স্বাস্থ্য', 'subtitle_en' => 'Complete care for women and children', 'subtitle_bn' => 'নারী ও শিশুর সম্পূর্ণ সেবা', 'accent' => '#f06b9b', 'image_path' => 'homepage/reference/service-women-child.png'],
                    ['title_en' => 'Emergency Care 24/7', 'title_bn' => 'জরুরি সেবা ২৪/৭', 'subtitle_en' => 'Round-the-clock emergency support & ambulance', 'subtitle_bn' => 'সার্বক্ষণিক জরুরি সেবা ও অ্যাম্বুলেন্স', 'accent' => '#d84e52', 'image_path' => 'homepage/reference/service-emergency.png'],
                ],
            ];
        }

        if ($key === 'diagnostic-test-categories') {
            return [
                'title_en' => 'Accurate Tests, Reliable Results',
                'title_bn' => 'আমাদের নির্ভরযোগ্য পরীক্ষা সমূহ',
                'groups' => self::diagnosticTestGroups(),
                'cta' => [
                    'title_en' => 'Accurate Tests, Reliable Results',
                    'title_bn' => 'সঠিক পরীক্ষা, সঠিক ফলাফল',
                    'button_label_en' => 'View All Tests',
                    'button_label_bn' => 'সব পরীক্ষা দেখুন',
                    'button_url' => '/en#tests',
                ],
            ];
        }

        if ($key === 'quality-highlight-strip') {
            return [
                'title_en' => 'Affordable, Accurate and Patient First',
                'title_bn' => 'সাশ্রয়ী, নির্ভুল ও রোগী-কেন্দ্রিক সেবা',
                'summary_en' => 'Quality healthcare at reasonable cost, reliable reports, experienced professionals, latest equipment and a clean environment.',
                'summary_bn' => 'সাশ্রয়ী খরচে মানসম্মত সেবা, নির্ভরযোগ্য রিপোর্ট, অভিজ্ঞ পেশাদার, আধুনিক যন্ত্রপাতি ও পরিষ্কার পরিবেশ।',
                'items' => [
                    ['title_en' => 'Affordable', 'title_bn' => 'সাশ্রয়ী', 'subtitle_en' => 'Quality healthcare at reasonable cost', 'subtitle_bn' => 'যৌক্তিক খরচে মানসম্মত সেবা'],
                    ['title_en' => 'Accurate & Reliable', 'title_bn' => 'নির্ভুল ও নির্ভরযোগ্য', 'subtitle_en' => '100% accurate tests with reliable reports', 'subtitle_bn' => 'নির্ভরযোগ্য রিপোর্টসহ সঠিক পরীক্ষা'],
                    ['title_en' => 'Expert Professionals', 'title_bn' => 'দক্ষ পেশাদার', 'subtitle_en' => 'Experienced doctors & technologists', 'subtitle_bn' => 'অভিজ্ঞ চিকিৎসক ও টেকনোলজিস্ট'],
                    ['title_en' => 'Modern Technology', 'title_bn' => 'আধুনিক প্রযুক্তি', 'subtitle_en' => 'Latest equipment for better diagnosis', 'subtitle_bn' => 'ভালো ডায়াগনোসিসের জন্য আধুনিক যন্ত্রপাতি'],
                    ['title_en' => 'Patient First', 'title_bn' => 'রোগী আগে', 'subtitle_en' => 'Your health, our top priority', 'subtitle_bn' => 'আপনার স্বাস্থ্য আমাদের অগ্রাধিকার'],
                    ['title_en' => 'Hygienic & Safe', 'title_bn' => 'পরিচ্ছন্ন ও নিরাপদ', 'subtitle_en' => 'Clean, sterile & safe environment', 'subtitle_bn' => 'পরিচ্ছন্ন ও নিরাপদ পরিবেশ'],
                ],
            ];
        }

        if (in_array($key, ['facility-showcase', 'health-packages', 'latest-articles'], true)) {
            $content = [
                'facility-showcase' => [
                    'title_en' => 'Comprehensive Care Under One Roof',
                    'title_bn' => 'আমাদের সুবিধা সমূহ',
                    'image_path' => 'homepage/reference/hospital-building.png',
                    'items' => [
                        ['title_en' => 'Indoor & Outdoor Patient Care', 'title_bn' => 'ইনডোর ও আউটডোর রোগী সেবা', 'subtitle_en' => 'Coordinated patient support', 'subtitle_bn' => 'সমন্বিত রোগী সহায়তা'],
                        ['title_en' => 'C-Section & Normal Delivery', 'title_bn' => 'সি-সেকশন ও নরমাল ডেলিভারি', 'subtitle_en' => 'Mother and child care', 'subtitle_bn' => 'মা ও শিশুর যত্ন'],
                        ['title_en' => 'Minor & Major Surgery', 'title_bn' => 'ছোট ও বড় অপারেশন', 'subtitle_en' => 'Surgical support', 'subtitle_bn' => 'সার্জিক্যাল সাপোর্ট'],
                        ['title_en' => 'General ICU', 'title_bn' => 'জেনারেল আইসিইউ', 'subtitle_en' => 'Critical care support', 'subtitle_bn' => 'ক্রিটিক্যাল কেয়ার সহায়তা'],
                        ['title_en' => 'Physiotherapy & Rehab', 'title_bn' => 'ফিজিওথেরাপি ও রিহ্যাব', 'subtitle_en' => 'Rehabilitation services', 'subtitle_bn' => 'পুনর্বাসন সেবা'],
                        ['title_en' => 'Pharmacy Services', 'title_bn' => 'ফার্মেসি সেবা', 'subtitle_en' => 'Medicine support', 'subtitle_bn' => 'ঔষধ সহায়তা'],
                    ],
                ],
                'health-packages' => [
                    'title_en' => 'Health Packages',
                    'title_bn' => 'হেলথ প্যাকেজ',
                    'items' => [
                        ['title_en' => 'Basic Health Checkup', 'title_bn' => 'বেসিক হেলথ চেকআপ', 'subtitle_en' => 'Essential tests for routine wellness checks', 'subtitle_bn' => 'নিয়মিত সুস্থতা যাচাইয়ের জন্য প্রয়োজনীয় পরীক্ষা'],
                        ['title_en' => 'Family Health Package', 'title_bn' => 'ফ্যামিলি হেলথ প্যাকেজ', 'subtitle_en' => 'Convenient screening support for the whole family', 'subtitle_bn' => 'পুরো পরিবারের জন্য সুবিধাজনক স্ক্রিনিং সাপোর্ট'],
                        ['title_en' => 'Diabetes Screening', 'title_bn' => 'ডায়াবেটিস স্ক্রিনিং', 'subtitle_en' => 'Focused testing support for sugar and metabolic health', 'subtitle_bn' => 'শর্করা ও মেটাবলিক স্বাস্থ্য যাচাইয়ের বিশেষ পরীক্ষা'],
                    ],
                ],
                'latest-articles' => [
                    'title_en' => 'Latest Articles',
                    'title_bn' => 'সাম্প্রতিক আর্টিকেল',
                    'items' => [
                        ['title_en' => 'How regular health checks help families', 'title_bn' => 'নিয়মিত স্বাস্থ্য পরীক্ষা পরিবারের জন্য কেন জরুরি', 'subtitle_en' => 'Practical guidance for preventive healthcare', 'subtitle_bn' => 'প্রতিরোধমূলক স্বাস্থ্যসেবার জন্য ব্যবহারিক পরামর্শ'],
                        ['title_en' => 'When physiotherapy can reduce pain', 'title_bn' => 'ব্যথা কমাতে ফিজিওথেরাপি কখন সহায়ক', 'subtitle_en' => 'Understanding recovery, movement and pain relief', 'subtitle_bn' => 'পুনরুদ্ধার, চলাচল ও ব্যথা উপশম সম্পর্কে জানুন'],
                        ['title_en' => 'Preparing for diagnostic tests', 'title_bn' => 'ডায়াগনস্টিক পরীক্ষার প্রস্তুতি', 'subtitle_en' => 'Simple steps before common lab tests', 'subtitle_bn' => 'সাধারণ ল্যাব পরীক্ষার আগে সহজ প্রস্তুতি'],
                    ],
                ],
            ];

            return [
                'title_en' => $content[$key]['title_en'],
                'title_bn' => $content[$key]['title_bn'],
                'image_path' => $content[$key]['image_path'] ?? null,
                'items' => $content[$key]['items'],
            ];
        }

        if ($key === 'physiotherapy-services') {
            $services = [
                ['Musculoskeletal physiotherapy', 'মাস্কুলোস্কেলেটাল ফিজিওথেরাপি'],
                ['Neurological rehabilitation', 'নিউরোলজিক্যাল রিহ্যাবিলিটেশন'],
                ['Stroke rehabilitation', 'স্ট্রোক রিহ্যাবিলিটেশন'],
                ['Sports injury rehabilitation', 'স্পোর্টস ইনজুরি রিহ্যাবিলিটেশন'],
                ['Post-operative rehabilitation', 'অপারেশন-পরবর্তী রিহ্যাবিলিটেশন'],
                ['Pediatric physiotherapy', 'শিশু ফিজিওথেরাপি'],
                ['Geriatric physiotherapy', 'বয়স্কদের ফিজিওথেরাপি'],
                ['Back and neck pain management', 'কোমর ও ঘাড় ব্যথা ব্যবস্থাপনা'],
                ['Frozen shoulder treatment', 'ফ্রোজেন শোল্ডার চিকিৎসা'],
                ['Arthritis and joint pain care', 'আর্থ্রাইটিস ও জয়েন্ট ব্যথার যত্ন'],
                ['Sciatica management', 'সায়াটিকা ব্যবস্থাপনা'],
                ['Manual therapy', 'ম্যানুয়াল থেরাপি'],
                ['Exercise therapy', 'এক্সারসাইজ থেরাপি'],
                ['Electrotherapy', 'ইলেক্ট্রোথেরাপি'],
                ['Balance and mobility training', 'ব্যালান্স ও চলাচল প্রশিক্ষণ'],
                ['Home physiotherapy', 'হোম ফিজিওথেরাপি'],
            ];

            return [
                'title_en' => 'Physiotherapy & Rehabilitation',
                'title_bn' => 'ফিজিওথেরাপি ও রিহ্যাবিলিটেশন',
                'subtitle_en' => 'Dedicated rehabilitation services for pain relief, mobility and recovery.',
                'subtitle_bn' => 'ব্যথা উপশম, চলাচল ও পুনরুদ্ধারের জন্য বিশেষায়িত রিহ্যাব সেবা।',
                'settings' => [
                    'columns_desktop' => 4,
                    'columns_tablet' => 2,
                    'columns_mobile' => 1,
                    'show_icons' => true,
                    'show_cta' => true,
                ],
                'services' => array_map(
                    static fn (array $service, int $index): array => [
                        'display_order' => $index + 1,
                        'is_active' => true,
                        'featured' => $index < 4,
                        'title_en' => $service[0],
                        'title_bn' => $service[1],
                        'description_en' => 'Focused care for pain relief, mobility and recovery.',
                        'description_bn' => 'ব্যথা উপশম, চলাচল ও পুনরুদ্ধারের জন্য বিশেষায়িত যত্ন।',
                        'cta_label_en' => 'Book physiotherapy',
                        'cta_label_bn' => 'ফিজিওথেরাপি বুক করুন',
                        'cta_url' => '/en#appointment-cta',
                    ],
                    $services,
                    array_keys($services)
                ),
            ];
        }

        if ($key === 'specialist-doctors') {
            return [
                'title_en' => 'Experienced. Caring. Dedicated.',
                'title_bn' => 'আমাদের বিশেষজ্ঞ চিকিৎসকবৃন্দ',
                'settings' => [
                    'auto_scroll' => true,
                    'pause_on_hover' => true,
                    'show_arrows' => true,
                    'show_dots' => false,
                    'loop' => true,
                    'cards_per_view_desktop' => 6,
                    'cards_per_view_mobile' => 1,
                ],
                'doctors' => [
                    [
                        'display_order' => 1,
                        'is_active' => true,
                        'featured' => true,
                        'call_enabled' => true,
                        'name_en' => 'Dr. Asifuzzaman',
                        'name_bn' => 'ডা. আসিফুজ্জামান',
                        'degrees' => 'MBBS, MD (Medicine)',
                        'specialty' => 'Consultant Physician',
                        'department' => 'Medicine',
                        'schedule_en' => 'Sat-Thu | 5:00 PM - 8:00 PM',
                        'schedule_bn' => 'শনি-বৃহস্পতিবার | বিকাল ৫টা - রাত ৮টা',
                        'profile_url' => '/en#doctors',
                        'appointment_url' => '/en#appointment-cta',
                        'call_url' => 'tel:+8801000000000',
                        'desktop_image_path' => 'homepage/reference/doctor-asifuzzaman.png',
                        'mobile_image_path' => 'homepage/reference/doctor-asifuzzaman.png',
                    ],
                    [
                        'display_order' => 2,
                        'is_active' => true,
                        'featured' => false,
                        'call_enabled' => true,
                        'name_en' => 'Dr. Jannatul Ferdous',
                        'name_bn' => 'ডা. জাহানারা ফেরদৌস',
                        'degrees' => 'MBBS, FCPS (Gynae & Obs)',
                        'specialty' => 'Gynaecologist & Obstetrician',
                        'department' => 'Gynecology',
                        'schedule_en' => 'Sun-Tue | 3:00 PM - 6:00 PM',
                        'schedule_bn' => 'রবি-মঙ্গল | বিকাল ৩টা - ৬টা',
                        'profile_url' => '/en#doctors',
                        'appointment_url' => '/en#appointment-cta',
                        'call_url' => 'tel:+8801000000001',
                        'desktop_image_path' => 'homepage/reference/doctor-jannatul.png',
                        'mobile_image_path' => 'homepage/reference/doctor-jannatul.png',
                    ],
                    [
                        'display_order' => 3,
                        'is_active' => true,
                        'featured' => false,
                        'call_enabled' => true,
                        'name_en' => 'Dr. Md. Kamrul Hasan',
                        'name_bn' => 'ডা. মোঃ কামরুল হাসান',
                        'degrees' => 'MBBS, MS (Surgery)',
                        'specialty' => 'General & Laparoscopic Surgeon',
                        'department' => 'Surgery',
                        'schedule_en' => 'Mon-Thu | 2:00 PM - 5:00 PM',
                        'schedule_bn' => 'সোম-বৃহস্পতিবার | দুপুর ২টা - বিকাল ৫টা',
                        'profile_url' => '/en#doctors',
                        'appointment_url' => '/en#appointment-cta',
                        'call_url' => 'tel:+8801000000002',
                        'desktop_image_path' => 'homepage/reference/doctor-kamrul.png',
                        'mobile_image_path' => 'homepage/reference/doctor-kamrul.png',
                    ],
                    [
                        'display_order' => 4,
                        'is_active' => true,
                        'featured' => false,
                        'call_enabled' => true,
                        'name_en' => 'Dr. Md. Mamunur Rashid',
                        'name_bn' => 'ডা. মোঃ মামুনুর রশিদ',
                        'degrees' => 'MBBS, MS (Ortho)',
                        'specialty' => 'Orthopedic Surgeon',
                        'department' => 'Orthopedics',
                        'schedule_en' => 'Tue-Fri | 6:00 PM - 9:00 PM',
                        'schedule_bn' => 'মঙ্গল-শুক্র | সন্ধ্যা ৬টা - ৯টা',
                        'profile_url' => '/en#doctors',
                        'appointment_url' => '/en#appointment-cta',
                        'call_url' => 'tel:+8801000000003',
                        'desktop_image_path' => 'homepage/reference/doctor-mamunur.png',
                        'mobile_image_path' => 'homepage/reference/doctor-mamunur.png',
                    ],
                    [
                        'display_order' => 5,
                        'is_active' => true,
                        'featured' => false,
                        'call_enabled' => true,
                        'name_en' => 'Dr. Sultana Rizia',
                        'name_bn' => 'ডা. সুলতানা রিজিয়া',
                        'degrees' => 'MBBS, BCS (Health)',
                        'specialty' => 'General Physician',
                        'department' => 'Medicine',
                        'schedule_en' => 'Daily | 10:00 AM - 1:00 PM',
                        'schedule_bn' => 'প্রতিদিন | সকাল ১০টা - দুপুর ১টা',
                        'profile_url' => '/en#doctors',
                        'appointment_url' => '/en#appointment-cta',
                        'call_url' => 'tel:+8801000000004',
                        'desktop_image_path' => 'homepage/reference/doctor-sultana.png',
                        'mobile_image_path' => 'homepage/reference/doctor-sultana.png',
                    ],
                    [
                        'display_order' => 6,
                        'is_active' => true,
                        'featured' => false,
                        'call_enabled' => true,
                        'name_en' => 'Dr. Tusher Ahmed',
                        'name_bn' => 'ডা. তুষার আহমেদ',
                        'degrees' => 'BPT, D.U, MPT (Neuro)',
                        'specialty' => 'Physiotherapy Specialist',
                        'department' => 'Physiotherapy',
                        'schedule_en' => 'Sat-Thu | 4:00 PM - 8:00 PM',
                        'schedule_bn' => 'শনি-বৃহস্পতিবার | বিকাল ৪টা - রাত ৮টা',
                        'profile_url' => '/en#doctors',
                        'appointment_url' => '/en#appointment-cta',
                        'call_url' => 'tel:+8801000000005',
                        'desktop_image_path' => 'homepage/reference/doctor-tusher.png',
                        'mobile_image_path' => 'homepage/reference/doctor-tusher.png',
                    ],
                ],
            ];
        }

        if ($key === 'why-choose-us') {
            return [
                'title_en' => 'Your Health Is Our Commitment',
                'title_bn' => 'স্বাস্থ্য সেবায় আমরা আপনার পাশে',
                'subtitle_en' => 'We combine experience, technology and compassion to deliver the best healthcare services.',
                'subtitle_bn' => 'অভিজ্ঞতা, প্রযুক্তি এবং সহানুভূতি মিলিয়ে আমরা সেরা স্বাস্থ্যসেবা প্রদান করি।',
                'points' => [
                    ['title_en' => 'Qualified & Experienced Doctors', 'title_bn' => 'দক্ষ ও অভিজ্ঞ ডাক্তার', 'description_en' => 'Specialists who care.', 'description_bn' => 'যত্নশীল বিশেষজ্ঞ দল।'],
                    ['title_en' => 'Modern Medical Equipment', 'title_bn' => 'আধুনিক চিকিৎসা সরঞ্জাম', 'description_en' => 'Technology-driven care.', 'description_bn' => 'প্রযুক্তিনির্ভর সেবা।'],
                    ['title_en' => 'Clean & Comfortable Environment', 'title_bn' => 'পরিষ্কার ও আরামদায়ক পরিবেশ', 'description_en' => 'Safe and welcoming spaces.', 'description_bn' => 'নিরাপদ ও স্বস্তিদায়ক পরিবেশ।'],
                    ['title_en' => 'Affordable Healthcare Services', 'title_bn' => 'সাশ্রয়ী স্বাস্থ্যসেবা', 'description_en' => 'Quality care at reasonable cost.', 'description_bn' => 'যৌক্তিক খরচে মানসম্মত সেবা।'],
                    ['title_en' => 'Emergency Care 24/7', 'title_bn' => '২৪/৭ জরুরি সেবা', 'description_en' => 'Always ready when you need us.', 'description_bn' => 'প্রয়োজনে আমরা সবসময় প্রস্তুত।'],
                ],
                'stats' => [
                    ['value' => '15+', 'title_en' => 'Expert Doctors', 'title_bn' => 'বিশেষজ্ঞ চিকিৎসক'],
                    ['value' => '10K+', 'title_en' => 'Happy Patients', 'title_bn' => 'সন্তুষ্ট রোগী'],
                    ['value' => '24/7', 'title_en' => 'Emergency Care', 'title_bn' => 'জরুরি সেবা'],
                    ['value' => '100+', 'title_en' => 'Tests Available', 'title_bn' => 'উপলব্ধ পরীক্ষা'],
                ],
            ];
        }

        if ($key === 'statistics') {
            return [
                'title_en' => 'Trusted by Patients',
                'title_bn' => 'রোগীদের আস্থার ঠিকানা',
                'stats' => [
                    ['value' => '15+', 'title_en' => 'Expert Doctors', 'title_bn' => 'বিশেষজ্ঞ চিকিৎসক'],
                    ['value' => '10K+', 'title_en' => 'Happy Patients', 'title_bn' => 'সন্তুষ্ট রোগী'],
                    ['value' => '24/7', 'title_en' => 'Emergency Care', 'title_bn' => 'জরুরি সেবা'],
                    ['value' => '100+', 'title_en' => 'Tests Available', 'title_bn' => 'উপলব্ধ পরীক্ষা'],
                ],
            ];
        }

        if ($key === 'testimonials') {
            return [
                'title_en' => 'Testimonials',
                'title_bn' => 'রোগীদের মতামত',
                'items' => [
                    ['title_en' => 'Trusted service', 'title_bn' => 'বিশ্বস্ত সেবা', 'subtitle_en' => 'Caring support from appointment to report delivery.', 'subtitle_bn' => 'অ্যাপয়েন্টমেন্ট থেকে রিপোর্ট ডেলিভারি পর্যন্ত যত্নশীল সহায়তা।'],
                    ['title_en' => 'Helpful staff', 'title_bn' => 'সহায়ক স্টাফ', 'subtitle_en' => 'Friendly guidance and clear communication.', 'subtitle_bn' => 'বন্ধুসুলভ দিকনির্দেশনা ও পরিষ্কার যোগাযোগ।'],
                    ['title_en' => 'Quick reports', 'title_bn' => 'দ্রুত রিপোর্ট', 'subtitle_en' => 'Reliable service with timely report support.', 'subtitle_bn' => 'সময়মতো রিপোর্ট সহায়তাসহ নির্ভরযোগ্য সেবা।'],
                ],
            ];
        }

        if ($key === 'home-sample-collection') {
            return [
                'title_en' => 'Home Sample Collection',
                'title_bn' => 'হোম স্যাম্পল কালেকশন',
                'content_en' => 'Safe, quick and reliable sample collection from home.',
                'content_bn' => 'বাড়ি থেকে নিরাপদ, দ্রুত ও নির্ভরযোগ্য নমুনা সংগ্রহ।',
                'points' => [
                    ['title_en' => 'Trained phlebotomists', 'title_bn' => 'প্রশিক্ষিত ফ্লেবোটোমিস্ট'],
                    ['title_en' => 'Hygienic collection process', 'title_bn' => 'স্বাস্থ্যসম্মত সংগ্রহ প্রক্রিয়া'],
                    ['title_en' => 'Timely delivery of reports', 'title_bn' => 'সময়মতো রিপোর্ট ডেলিভারি'],
                ],
                'button_label_en' => 'Book Home Collection',
                'button_label_bn' => 'হোম কালেকশন বুক করুন',
                'button_url' => '/en#appointment-cta',
                'image_path' => 'homepage/reference/home-sample-nurse.png',
            ];
        }

        if ($key === 'appointment-cta') {
            return [
                'title_en' => 'Need an Appointment?',
                'title_bn' => 'অ্যাপয়েন্টমেন্ট প্রয়োজন?',
                'content_en' => 'Book an appointment with our expert doctors today and take the first step towards better health.',
                'content_bn' => 'আমাদের বিশেষজ্ঞ চিকিৎসকের সাথে আজই অ্যাপয়েন্টমেন্ট নিন এবং সুস্থতার পথে প্রথম পদক্ষেপ রাখুন।',
                'primary_cta_label_en' => 'Book Appointment',
                'primary_cta_label_bn' => 'অ্যাপয়েন্টমেন্ট নিন',
                'primary_cta_url' => '/en#appointment-cta',
                'secondary_cta_label_en' => 'Call Us Now',
                'secondary_cta_label_bn' => 'এখনই কল করুন',
                'secondary_cta_url' => 'tel:+8801777XXXXXX',
                'image_path' => 'homepage/reference/appointment-doctor.png',
            ];
        }

        return [];
    }

    public static function diagnosticTestGroups(): array
    {
        $groups = [
            'Blood Tests' => [
                'TC, DC, Hb%, ESR (Blood CP)',
                'CBC, ESR (Cell Counter)',
                'CE (Circulating Eosinophil)',
                'Blood Film (PBF)',
                'HB-Electrophoresis',
                'BT, CT',
                'Blood Group & RH Factor',
                'RBS (Random Blood Sugar)',
                'Blood Sugar Fasting',
                '2 Hours After Breakfast (2 HABF)',
                '2 Hours After 75 gm Glucose Drink',
                'OGTT',
                'MP (Malaria Parasite)',
                'Widal Test',
                'ASO Titre',
                'R.A. Test',
                'CRP',
                'TPHA',
                'VDRL (Qualitative & Quantitative)',
                'S Bilirubin',
                'S Bilirubin (Direct & Indirect)',
                'HBs Ag (Latex)',
                'HBs Ag (ELISA)',
                'SGPT (ALT)',
                'SGOT (AST)',
                'Prothrombin Time',
                'Serum Amylase',
                'Serum Lipase',
                'Serum Albumin',
                'Serum Total Protein / A:G Ratio',
                'Serum Alkaline Phosphatase',
                'Serum Creatinine',
                'Cross Match (Screening Test)',
                'Serum Urea',
                'Serum Uric Acid',
                'Serum Calcium',
                'Lipid Profile',
                'Serum Cholesterol',
                'Serum Triglyceride',
                'Serum Electrolyte',
                'Urinary Electrolyte',
                'HbA1C',
            ],
            'Urine Tests' => [
                'Urine C/S',
                'Urine R/E',
                'Urine PT (Pregnancy Test)',
                '24 Hours Total Protein (UTP)',
            ],
            'Semen Test' => [
                'Semen Analysis',
            ],
            'Microbiology Tests' => [
                'Blood for C/S (Automated Fan M)',
                'Urethral Smear for C/S',
                'Pus for C/S',
                'Stool for C/S',
                'Beta hCG',
                'Iron',
                'TIBC',
                'Iron Profile',
                'Anti CCP',
                'CA-19.9',
                'PSA',
            ],
            'Stool Tests' => [
                'Stool R/M/E',
                'Stool OBT',
            ],
            'Serology / Immunology' => [
                'ICT for TB',
                'Dengue NS1',
                'Anti H. Pylori IgG',
                'Febric Ag Test',
            ],
            'Thyroid / Hormone Tests' => [
                'Total Triiodothyronine (T3)',
                'Total Thyroxine (T4)',
                'Thyroid Stimulating Hormone (TSH)',
                'Free Triiodothyronine (FT3)',
                'Free Thyroxine (FT4)',
                'T3, T4 & TSH',
            ],
            'Cardiac Tests' => [
                'CPK',
                'Troponin I',
                'ECG (12 Channel)',
                'Echocardiography',
            ],
            'Infertility / Reproductive Hormones' => [
                'LH',
                'FSH',
                'Prolactin',
                'Testosterone',
                'Progesterone',
                'Estradiol / Estrogen',
            ],
            'Tuberculosis / Hepatitis / Infection' => [
                'MT (Mantoux Test)',
                'CA-125',
                'Anti-HCV (ELISA)',
                'Anti-HCV (Latex)',
                'Anti-HBs',
                'Anti-HAV',
                'HBeAg (ELISA)',
                'LFT',
                'ICT for Malaria',
                'ICT for Filaria',
                'ICT for Kala Azar',
            ],
            'Digital X-Ray' => [
                'X-ray Chest (P/A, A/P, Apical)',
                'X-ray Skull (A/P, Oblique, Lateral)',
                'Shoulder Joint (L/R)',
                'Hip Joint (L/R)',
                'Knee Joint (L/R)',
                'Ankle Joint (L/R)',
                'PNS (OM, Lateral View)',
                'Nasopharynx (True Lateral View)',
                'Pelvis (A/P, Frog Leg View)',
                'Cervical Spine',
                'Dorsal Spine',
                'L/S Spine',
                'S.I. Joint (Both)',
                'Soft Tissue Neck',
                'Abdomen (Erect/Supine)',
                'Nasal Bone (Both View)',
                'Mandible (Both View)',
                'TM Joint (Open & Close Mouth)',
                "Mastoid (Towne's, Stenver's View)",
                'X-ray KUB',
                'X-ray Leg (B/V)',
                'X-ray Thigh (B/V)',
                'X-ray Hand (B/V)',
                'X-ray Wrist Joint (B/V)',
                'X-ray Forearm (B/V)',
                'X-ray Elbow Joint (B/V)',
                'X-ray Arm (B/V)',
                'X-ray OPG',
                'X-ray Dental',
            ],
            'Ultrasonography' => [
                'Whole Abdomen',
                'Lower Abdomen',
                'HBS & Pancreas',
                'Pelvic Organs',
                'Scrotum / Testies',
                'Upper Abdomen',
                'HBS / Liver / Gall Bladder',
                'Uterus & Adnexae',
                'Pregnancy Profile',
                'Parotid Gland',
            ],
        ];

        return collect($groups)->map(fn (array $tests, string $title): array => [
            'title_en' => $title,
            'title_bn' => $title,
            'tests_en' => $tests,
            'tests_bn' => $tests,
        ])->values()->all();
    }
}
