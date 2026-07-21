<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('homepage_sections')) {
            return;
        }

        foreach ($this->labels() as $key => $label) {
            DB::table('homepage_sections')
                ->where('section_key', $key)
                ->update([
                    'section_label_en' => $label['en'],
                    'section_label_bn' => $label['bn'],
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('homepage_sections')) {
            return;
        }

        $oldLabels = [
            'hero-slider' => ['en' => 'Hero Slider', 'bn' => 'হিরো স্লাইডার'],
            'main-services' => ['en' => 'Main Services', 'bn' => 'প্রধান সেবা'],
            'diagnostic-test-categories' => ['en' => 'Diagnostic Test Categories', 'bn' => 'ডায়াগনস্টিক টেস্ট বিভাগ'],
            'specialist-doctors' => ['en' => 'Specialist Doctors', 'bn' => 'বিশেষজ্ঞ ডাক্তার'],
        ];

        foreach ($oldLabels as $key => $label) {
            DB::table('homepage_sections')
                ->where('section_key', $key)
                ->update([
                    'section_label_en' => $label['en'],
                    'section_label_bn' => $label['bn'],
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * @return array<string, array{en: string, bn: string}>
     */
    private function labels(): array
    {
        return [
            'hero-slider' => ['en' => 'Hero Image', 'bn' => 'হিরো ছবি'],
            'main-services' => ['en' => 'Our Services', 'bn' => 'আমাদের সেবা সমূহ'],
            'diagnostic-test-categories' => ['en' => 'Accurate Tests, Reliable Results', 'bn' => 'নির্ভুল পরীক্ষা, নির্ভরযোগ্য রিপোর্ট'],
            'specialist-doctors' => ['en' => 'Specialist Doctors', 'bn' => 'বিশেষজ্ঞ ডাক্তার'],
        ];
    }
};
