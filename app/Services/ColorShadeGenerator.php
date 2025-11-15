<?php

declare(strict_types=1);

namespace App\Services;

class ColorShadeGenerator
{
    /**
     * Generate Tailwind-style color shades (50-950) from a base hex color.
     *
     * @param  string  $hexColor  Base color in hex format (e.g., '#0ea5e9')
     * @return array<int, string> Array of shades with numeric keys (50, 100, 200...950)
     */
    public function generate(string $hexColor): array
    {
        // Remove # if present
        $hex = ltrim($hexColor, '#');

        // Convert hex to RGB
        [$r, $g, $b] = $this->hexToRgb($hex);

        // Convert to HSL for easier manipulation
        [$h, $s, $l] = $this->rgbToHsl($r, $g, $b);

        // Generate shades based on lightness adjustments
        // Tailwind scale: 50 (lightest) -> 500 (base) -> 950 (darkest)
        $shades = [
            50 => $this->adjustLightness($h, $s, $l, 0.95),   // Very light
            100 => $this->adjustLightness($h, $s, $l, 0.90),  // Light
            200 => $this->adjustLightness($h, $s, $l, 0.80),  // Light-medium
            300 => $this->adjustLightness($h, $s, $l, 0.65),  // Medium-light
            400 => $this->adjustLightness($h, $s, $l, 0.55),  // Medium
            500 => $hexColor,                                  // Base color
            600 => $this->adjustLightness($h, $s, $l, 0.42),  // Medium-dark
            700 => $this->adjustLightness($h, $s, $l, 0.35),  // Dark
            800 => $this->adjustLightness($h, $s, $l, 0.25),  // Darker
            900 => $this->adjustLightness($h, $s, $l, 0.18),  // Very dark
            950 => $this->adjustLightness($h, $s, $l, 0.10),  // Darkest
        ];

        return $shades;
    }

    /**
     * Convert hex color to RGB.
     *
     * @return array{0: int, 1: int, 2: int}
     */
    protected function hexToRgb(string $hex): array
    {
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Convert RGB to HSL.
     *
     * @return array{0: float, 1: float, 2: float}
     */
    protected function rgbToHsl(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            $h = $s = 0; // Achromatic
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            $h = match ($max) {
                $r => (($g - $b) / $d + ($g < $b ? 6 : 0)),
                $g => (($b - $r) / $d + 2),
                $b => (($r - $g) / $d + 4),
                default => 0,
            };

            $h /= 6;
        }

        return [$h, $s, $l];
    }

    /**
     * Convert HSL back to hex color with adjusted lightness.
     */
    protected function adjustLightness(float $h, float $s, float $l, float $targetLightness): string
    {
        // For very light shades, reduce saturation slightly
        if ($targetLightness > 0.85) {
            $s *= 0.7;
        }

        // For very dark shades, reduce saturation slightly
        if ($targetLightness < 0.20) {
            $s *= 0.9;
        }

        $rgb = $this->hslToRgb($h, $s, $targetLightness);

        return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }

    /**
     * Convert HSL to RGB.
     *
     * @return array{0: int, 1: int, 2: int}
     */
    protected function hslToRgb(float $h, float $s, float $l): array
    {
        if ($s === 0.0) {
            $r = $g = $b = $l; // Achromatic
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $this->hueToRgb($p, $q, $h + 1 / 3);
            $g = $this->hueToRgb($p, $q, $h);
            $b = $this->hueToRgb($p, $q, $h - 1 / 3);
        }

        return [
            (int) round($r * 255),
            (int) round($g * 255),
            (int) round($b * 255),
        ];
    }

    /**
     * Helper function for HSL to RGB conversion.
     */
    protected function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) {
            $t += 1;
        }
        if ($t > 1) {
            $t -= 1;
        }
        if ($t < 1 / 6) {
            return $p + ($q - $p) * 6 * $t;
        }
        if ($t < 1 / 2) {
            return $q;
        }
        if ($t < 2 / 3) {
            return $p + ($q - $p) * (2 / 3 - $t) * 6;
        }

        return $p;
    }
}
