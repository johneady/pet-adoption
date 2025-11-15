<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    public function __construct(
        protected ColorShadeGenerator $colorGenerator
    ) {}

    /**
     * Get the current theme preset name.
     */
    public function getCurrentThemePreset(): string
    {
        return Setting::get('theme_preset', 'ocean-blue');
    }

    /**
     * Get the theme colors (primary and secondary) for the current theme.
     *
     * @return array{primary: string, secondary: string}
     */
    public function getThemeColors(): array
    {
        $preset = $this->getCurrentThemePreset();
        $presets = config('theme-presets');

        // Check for custom colors first
        $customPrimary = Setting::get('theme_primary_color');
        $customSecondary = Setting::get('theme_secondary_color');

        return [
            'primary' => $customPrimary ?: ($presets[$preset]['primary'] ?? '#0ea5e9'),
            'secondary' => $customSecondary ?: ($presets[$preset]['secondary'] ?? '#14b8a6'),
        ];
    }

    /**
     * Generate CSS variables for the current theme.
     */
    public function generateThemeCSS(): string
    {
        $colors = $this->getThemeColors();

        // Generate shades for both colors
        $primaryShades = $this->colorGenerator->generate($colors['primary']);
        $secondaryShades = $this->colorGenerator->generate($colors['secondary']);

        // Build CSS string
        $css = ':root {'.PHP_EOL;

        // Replace ocean-* with primary color shades
        foreach ($primaryShades as $shade => $hex) {
            $css .= "    --color-ocean-{$shade}: {$hex};".PHP_EOL;
        }

        // Replace teal-* with secondary color shades
        foreach ($secondaryShades as $shade => $hex) {
            $css .= "    --color-teal-{$shade}: {$hex};".PHP_EOL;
        }

        $css .= '}';

        return $css;
    }

    /**
     * Get cached theme CSS or generate if not cached.
     */
    public function getCachedThemeCSS(): string
    {
        $preset = $this->getCurrentThemePreset();
        $cacheKey = "theme_css_{$preset}";

        return Cache::remember($cacheKey, now()->addDay(), function () {
            return $this->generateThemeCSS();
        });
    }

    /**
     * Clear the theme CSS cache.
     */
    public function clearCache(): void
    {
        $preset = $this->getCurrentThemePreset();
        Cache::forget("theme_css_{$preset}");

        // Also clear all possible theme preset caches
        $presets = array_keys(config('theme-presets'));
        foreach ($presets as $presetKey) {
            Cache::forget("theme_css_{$presetKey}");
        }
    }

    /**
     * Get all available theme presets.
     *
     * @return array<string, array{label: string, primary: string, secondary: string}>
     */
    public function getAvailablePresets(): array
    {
        return config('theme-presets');
    }

    /**
     * Get preset options formatted for Filament select field.
     *
     * @return array<string, string>
     */
    public function getPresetOptions(): array
    {
        $presets = $this->getAvailablePresets();
        $options = [];

        foreach ($presets as $key => $preset) {
            $options[$key] = $preset['label'];
        }

        return $options;
    }
}
