<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Models\User;
use App\Services\ColorShadeGenerator;
use App\Services\ThemeService;
use Illuminate\Support\Facades\Cache;

test('theme preset setting exists in database after migration', function () {
    $themeSetting = Setting::where('key', 'theme_preset')->first();

    expect($themeSetting)->not->toBeNull()
        ->and($themeSetting->value)->toBe('navy-gold')
        ->and($themeSetting->group)->toBe('theme');
});

test('color shade generator creates correct number of shades', function () {
    $generator = new ColorShadeGenerator;
    $shades = $generator->generate('#0ea5e9');

    expect($shades)->toBeArray()
        ->toHaveCount(11)
        ->toHaveKeys([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950]);
});

test('color shade generator returns base color for 500 shade', function () {
    $generator = new ColorShadeGenerator;
    $baseColor = '#0ea5e9';
    $shades = $generator->generate($baseColor);

    expect($shades[500])->toBe($baseColor);
});

test('theme service returns default navy-gold preset', function () {
    $themeService = app(ThemeService::class);

    expect($themeService->getCurrentThemePreset())->toBe('navy-gold');
});

test('theme service returns correct colors for preset', function () {
    $themeService = app(ThemeService::class);
    $colors = $themeService->getThemeColors();

    expect($colors)->toHaveKeys(['primary', 'secondary'])
        ->and($colors['primary'])->toBe('#1e40af')
        ->and($colors['secondary'])->toBe('#f59e0b');
});

test('theme service generates valid css', function () {
    $themeService = app(ThemeService::class);
    $css = $themeService->generateThemeCSS();

    expect($css)->toContain(':root')
        ->toContain('--color-ocean-')
        ->toContain('--color-teal-')
        ->toContain('--color-ocean-50')
        ->toContain('--color-ocean-500')
        ->toContain('--color-ocean-950')
        ->toContain('--color-teal-50')
        ->toContain('--color-teal-500')
        ->toContain('--color-teal-950');
});

test('theme service caches generated css', function () {
    Cache::flush();

    $themeService = app(ThemeService::class);

    // First call should cache
    $css1 = $themeService->getCachedThemeCSS();

    // Second call should use cache
    $css2 = $themeService->getCachedThemeCSS();

    expect($css1)->toBe($css2);
});

test('theme service clears cache correctly', function () {
    $themeService = app(ThemeService::class);

    // Generate and cache CSS
    $themeService->getCachedThemeCSS();

    // Clear cache
    $themeService->clearCache();

    // Cache key should not exist
    expect(Cache::has('theme_css_navy-gold'))->toBeFalse();
});

test('changing theme preset clears theme cache', function () {
    $themeService = app(ThemeService::class);

    // Generate and cache CSS
    $themeService->getCachedThemeCSS();

    // Change theme preset
    $setting = Setting::where('key', 'theme_preset')->first();
    $setting->value = 'forest-green';
    $setting->save();

    // Old cache should be cleared
    expect(Cache::has('theme_css_navy-gold'))->toBeFalse();
});

test('theme service returns all available presets', function () {
    $themeService = app(ThemeService::class);
    $presets = $themeService->getAvailablePresets();

    expect($presets)->toBeArray()
        ->toHaveKey('ocean-blue')
        ->toHaveKey('forest-green')
        ->toHaveKey('sunset-orange')
        ->toHaveKey('royal-purple');
});

test('theme service returns preset options for filament', function () {
    $themeService = app(ThemeService::class);
    $options = $themeService->getPresetOptions();

    expect($options)->toBeArray()
        ->and($options['ocean-blue'])->toBe('Ocean Blue')
        ->and($options['forest-green'])->toBe('Forest Green');
});

test('admin can access theme settings in filament', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/admin')
        ->assertSuccessful();
});
