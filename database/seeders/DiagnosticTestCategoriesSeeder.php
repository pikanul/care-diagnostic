<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use App\Support\HomepageBuilder;
use Illuminate\Database\Seeder;

class DiagnosticTestCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $section = HomepageSection::query()->where('section_key', 'diagnostic-test-categories')->first();

        if (! $section) {
            return;
        }

        $defaults = HomepageBuilder::defaultContent('diagnostic-test-categories');

        $section->update([
            'section_label_en' => 'Accurate Tests, Reliable Results',
            'section_label_bn' => 'নির্ভুল পরীক্ষা, নির্ভরযোগ্য রিপোর্ট',
            'title_en' => $defaults['title_en'],
            'title_bn' => $defaults['title_bn'],
            'section_data' => $defaults,
            'display_limit' => 5,
            'is_active' => true,
        ]);
    }
}
