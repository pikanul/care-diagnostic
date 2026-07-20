<?php

namespace App\Providers;

use App\Models\FooterSection;
use App\Models\GlobalSetting;
use App\Models\NavigationItem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            if (! Schema::hasTable('global_settings')) {
                $view->with([
                    'siteSettings' => null,
                    'headerNavigationItems' => collect(),
                    'footerSections' => collect(),
                ]);

                return;
            }

            $settings = GlobalSetting::current();

            $view->with([
                'siteSettings' => $settings,
                'headerNavigationItems' => NavigationItem::query()
                    ->where('location', 'header')
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('display_order')
                    ->with(['children' => function ($query): void {
                        $query->where('is_active', true)->orderBy('display_order');
                    }])
                    ->get(),
                'footerSections' => FooterSection::query()
                    ->where('is_active', true)
                    ->orderBy('display_order')
                    ->with(['links' => function ($query): void {
                        $query->where('is_active', true)->orderBy('display_order');
                    }])
                    ->get(),
            ]);
        });
    }
}
