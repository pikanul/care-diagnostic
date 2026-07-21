<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FooterLinkController;
use App\Http\Controllers\Admin\FooterSectionController;
use App\Http\Controllers\Admin\FoundationPageController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\GlobalSettingController;
use App\Http\Controllers\Admin\NavigationItemController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Public\NotFoundController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\Public\SeoController;
use App\Http\Controllers\Public\WebVitalController;
use App\Http\Middleware\SetLocale;
use App\Support\AdminPermissions;
use App\Models\GlobalSetting;
use App\Models\HomepageSection;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $locale = session('locale', config('app.locale'));
    $defaultLocale = GlobalSetting::current()->default_language ?? config('app.locale');

    if (! in_array($locale, SetLocale::SUPPORTED_LOCALES, true)) {
        $locale = $defaultLocale;
    }

    if (! in_array($locale, SetLocale::SUPPORTED_LOCALES, true)) {
        $locale = 'en';
    }

    return redirect()->to("/{$locale}");
})->name('home.redirect');

Route::prefix('{locale}')
    ->whereIn('locale', SetLocale::SUPPORTED_LOCALES)
    ->middleware('locale')
    ->group(function () {
        Route::get('/', function () {
            return view('home', [
                'homepageSections' => HomepageSection::query()
                    ->where('is_active', true)
                    ->whereNotIn('section_key', ['top-information-bar', 'main-navigation', 'footer'])
                    ->orderBy('display_order')
                    ->get(),
            ]);
        })->name('home');
    });

Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::post('/web-vitals', [WebVitalController::class, 'store'])->name('web-vitals.store');

Route::get('/{locale}/search/suggest', [SearchController::class, 'suggest'])
    ->whereIn('locale', SetLocale::SUPPORTED_LOCALES)
    ->middleware('locale')
    ->name('search.suggest');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::middleware(['admin.auth', 'admin.active'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::get('/homepage', [HomepageSectionController::class, 'index'])
            ->middleware('permission:'.AdminPermissions::MANAGE_CONTENT)
            ->name('homepage.index');

        Route::get('/homepage/{homepageSection}/edit', [HomepageSectionController::class, 'edit'])
            ->middleware('permission:'.AdminPermissions::MANAGE_CONTENT)
            ->name('homepage.edit');

        Route::put('/homepage/{homepageSection}', [HomepageSectionController::class, 'update'])
            ->middleware('permission:'.AdminPermissions::MANAGE_CONTENT)
            ->name('homepage.update');

        Route::post('/homepage/{homepageSection}/toggle', [HomepageSectionController::class, 'toggle'])
            ->middleware('permission:'.AdminPermissions::MANAGE_CONTENT)
            ->name('homepage.toggle');

        Route::post('/homepage/reorder', [HomepageSectionController::class, 'reorder'])
            ->middleware('permission:'.AdminPermissions::MANAGE_CONTENT)
            ->name('homepage.reorder');

        Route::get('/global-settings', [GlobalSettingController::class, 'edit'])
            ->middleware('permission:'.AdminPermissions::MANAGE_GLOBAL_SETTINGS)
            ->name('global-settings.edit');

        Route::put('/global-settings', [GlobalSettingController::class, 'update'])
            ->middleware('permission:'.AdminPermissions::MANAGE_GLOBAL_SETTINGS)
            ->name('global-settings.update');

        Route::get('/navigation', [NavigationItemController::class, 'index'])
            ->middleware('permission:'.AdminPermissions::MANAGE_NAVIGATION)
            ->name('navigation.index');

        Route::post('/navigation', [NavigationItemController::class, 'store'])
            ->middleware('permission:'.AdminPermissions::MANAGE_NAVIGATION)
            ->name('navigation.store');

        Route::get('/navigation/{navigationItem}/edit', [NavigationItemController::class, 'edit'])
            ->middleware('permission:'.AdminPermissions::MANAGE_NAVIGATION)
            ->name('navigation.edit');

        Route::put('/navigation/{navigationItem}', [NavigationItemController::class, 'update'])
            ->middleware('permission:'.AdminPermissions::MANAGE_NAVIGATION)
            ->name('navigation.update');

        Route::delete('/navigation/{navigationItem}', [NavigationItemController::class, 'destroy'])
            ->middleware('permission:'.AdminPermissions::MANAGE_NAVIGATION)
            ->name('navigation.destroy');

        Route::post('/navigation/{navigationItem}/restore', [NavigationItemController::class, 'restore'])
            ->middleware('permission:'.AdminPermissions::MANAGE_NAVIGATION)
            ->name('navigation.restore');

        Route::post('/navigation/{navigationItem}/toggle', [NavigationItemController::class, 'toggle'])
            ->middleware('permission:'.AdminPermissions::MANAGE_NAVIGATION)
            ->name('navigation.toggle');

        Route::post('/navigation/reorder', [NavigationItemController::class, 'reorder'])
            ->middleware('permission:'.AdminPermissions::MANAGE_NAVIGATION)
            ->name('navigation.reorder');

        Route::get('/footer-sections', [FooterSectionController::class, 'index'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-sections.index');

        Route::post('/footer-sections', [FooterSectionController::class, 'store'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-sections.store');

        Route::get('/footer-sections/{footerSection}/edit', [FooterSectionController::class, 'edit'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-sections.edit');

        Route::put('/footer-sections/{footerSection}', [FooterSectionController::class, 'update'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-sections.update');

        Route::delete('/footer-sections/{footerSection}', [FooterSectionController::class, 'destroy'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-sections.destroy');

        Route::post('/footer-sections/{footerSection}/restore', [FooterSectionController::class, 'restore'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-sections.restore');

        Route::post('/footer-sections/{footerSection}/toggle', [FooterSectionController::class, 'toggle'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-sections.toggle');

        Route::post('/footer-sections/reorder', [FooterSectionController::class, 'reorder'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-sections.reorder');

        Route::get('/footer-links', [FooterLinkController::class, 'index'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-links.index');

        Route::post('/footer-links', [FooterLinkController::class, 'store'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-links.store');

        Route::get('/footer-links/{footerLink}/edit', [FooterLinkController::class, 'edit'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-links.edit');

        Route::put('/footer-links/{footerLink}', [FooterLinkController::class, 'update'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-links.update');

        Route::delete('/footer-links/{footerLink}', [FooterLinkController::class, 'destroy'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-links.destroy');

        Route::post('/footer-links/{footerLink}/restore', [FooterLinkController::class, 'restore'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-links.restore');

        Route::post('/footer-links/{footerLink}/toggle', [FooterLinkController::class, 'toggle'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-links.toggle');

        Route::post('/footer-links/reorder', [FooterLinkController::class, 'reorder'])
            ->middleware('permission:'.AdminPermissions::MANAGE_FOOTER)
            ->name('footer-links.reorder');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:'.AdminPermissions::MANAGE_USERS)
            ->name('users.index');

        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:'.AdminPermissions::MANAGE_USERS)
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:'.AdminPermissions::MANAGE_USERS)
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:'.AdminPermissions::MANAGE_USERS)
            ->name('users.update');

        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])
            ->middleware('permission:'.AdminPermissions::MANAGE_USERS)
            ->name('users.toggle');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:'.AdminPermissions::MANAGE_USERS)
            ->name('users.destroy');

        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:'.AdminPermissions::MANAGE_ROLES)
            ->name('roles.index');

        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:'.AdminPermissions::MANAGE_ROLES)
            ->name('roles.store');

        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:'.AdminPermissions::MANAGE_ROLES)
            ->name('roles.edit');

        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:'.AdminPermissions::MANAGE_ROLES)
            ->name('roles.update');

        Route::post('/roles/{role}/toggle', [RoleController::class, 'toggle'])
            ->middleware('permission:'.AdminPermissions::MANAGE_ROLES)
            ->name('roles.toggle');

        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:'.AdminPermissions::MANAGE_ROLES)
            ->name('roles.destroy');

        Route::post('/roles/{role}/restore', [RoleController::class, 'restore'])
            ->middleware('permission:'.AdminPermissions::MANAGE_ROLES)
            ->name('roles.restore');

        Route::get('/backups', FoundationPageController::class)
            ->defaults('section', 'backups')
            ->middleware('permission:'.AdminPermissions::MANAGE_BACKUPS)
            ->name('backups.index');

        Route::get('/security', FoundationPageController::class)
            ->defaults('section', 'security')
            ->middleware('permission:'.AdminPermissions::MANAGE_SECURITY)
            ->name('security.index');

        Route::get('/api-credentials', FoundationPageController::class)
            ->defaults('section', 'api-credentials')
            ->middleware('permission:'.AdminPermissions::MANAGE_API_CREDENTIALS)
            ->name('api-credentials.index');

        Route::get('/destructive-actions', FoundationPageController::class)
            ->defaults('section', 'destructive-actions')
            ->middleware('permission:'.AdminPermissions::GLOBAL_DESTRUCTIVE_ACTIONS)
            ->name('destructive-actions.index');
    });
});

Route::fallback(NotFoundController::class);
