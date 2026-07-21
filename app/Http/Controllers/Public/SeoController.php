<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\HomepageSection;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect();

        foreach (SetLocale::SUPPORTED_LOCALES as $locale) {
            $urls->push(url('/'.$locale));

            HomepageSection::query()
                ->where('is_active', true)
                ->whereNotIn('section_key', ['top-information-bar', 'main-navigation', 'footer'])
                ->orderBy('display_order')
                ->pluck('section_key')
                ->each(fn (string $key) => $urls->push(url('/'.$locale.'#'.$key)));
        }

        return response()
            ->view('seo.sitemap', ['urls' => $urls->unique()->values()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $settings = GlobalSetting::current();
        $robots = data_get($settings->seo_settings, 'robots_txt');

        if (! $robots) {
            $robots = "User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n";
        }

        return response($robots, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
