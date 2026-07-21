<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggest(Request $request, string $locale): JsonResponse
    {
        $query = mb_strtolower(trim((string) $request->query('q', '')));

        if (mb_strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = collect();

        HomepageSection::query()
            ->whereIn('section_key', ['main-services', 'diagnostic-test-categories', 'specialist-doctors', 'physiotherapy-services'])
            ->where('is_active', true)
            ->get()
            ->each(function (HomepageSection $section) use ($locale, $query, $results): void {
                $data = $section->section_data ?? [];

                if ($section->section_key === 'specialist-doctors') {
                    foreach (($data['doctors'] ?? []) as $doctor) {
                        $this->pushResult($results, $query, [
                            'type' => 'doctor',
                            'title' => $doctor['name_'.$locale] ?? $doctor['name_en'] ?? '',
                            'subtitle' => collect([$doctor['degrees'] ?? null, $doctor['specialty'] ?? null, $doctor['department'] ?? null])->filter()->join(' · '),
                            'url' => $doctor['profile_url'] ?? '/'.$locale.'#specialist-doctors',
                        ]);
                    }
                }

                if (in_array($section->section_key, ['main-services', 'physiotherapy-services'], true)) {
                    foreach (($data['items'] ?? $data['services'] ?? []) as $service) {
                        $this->pushResult($results, $query, [
                            'type' => 'service',
                            'title' => $service['title_'.$locale] ?? $service['title_en'] ?? '',
                            'subtitle' => $service['description_'.$locale] ?? $service['description_en'] ?? '',
                            'url' => $service['cta_url'] ?? '/'.$locale.'#'.$section->section_key,
                        ]);
                    }
                }

                if ($section->section_key === 'diagnostic-test-categories') {
                    foreach (($data['categories'] ?? []) as $category) {
                        $categoryTitle = $category['title_'.$locale] ?? $category['title_en'] ?? '';
                        $this->pushResult($results, $query, [
                            'type' => 'test-category',
                            'title' => $categoryTitle,
                            'subtitle' => 'Diagnostic category',
                            'url' => '/'.$locale.'#diagnostic-test-categories',
                        ]);

                        foreach (($category['tests'] ?? []) as $test) {
                            $testTitle = is_array($test) ? ($test['title_'.$locale] ?? $test['title_en'] ?? '') : (string) $test;
                            $this->pushResult($results, $query, [
                                'type' => 'test',
                                'title' => $testTitle,
                                'subtitle' => $categoryTitle,
                                'url' => '/'.$locale.'#diagnostic-test-categories',
                            ]);
                        }
                    }
                }
            });

        return response()->json([
            'results' => $results->unique(fn (array $row): string => $row['type'].'|'.$row['title'])->take(12)->values(),
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array<string, string>>  $results
     * @param  array<string, string>  $row
     */
    private function pushResult($results, string $query, array $row): void
    {
        $title = trim((string) ($row['title'] ?? ''));

        if ($title === '') {
            return;
        }

        $haystack = mb_strtolower($title.' '.($row['subtitle'] ?? ''));

        if (str_contains($haystack, $query)) {
            $results->push($row);
        }
    }
}
