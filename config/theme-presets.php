<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Color Presets
    |--------------------------------------------------------------------------
    |
    | Define preset color themes for the frontend. Each theme includes a
    | primary and secondary base color (hex format). The system will
    | automatically generate all Tailwind shades (50-950) from these.
    |
    */

    // Updated presets with better complementary colors
    'ocean-blue' => [
        'label' => 'Ocean Blue',
        'primary' => '#0ea5e9',    // sky-500
        'secondary' => '#fb923c',  // orange-400 (warm complement)
    ],

    'forest-green' => [
        'label' => 'Forest Green',
        'primary' => '#10b981',    // emerald-500
        'secondary' => '#f59e0b',  // amber-500 (warm earthy complement)
    ],

    'sunset-orange' => [
        'label' => 'Sunset Orange',
        'primary' => '#f97316',    // orange-500
        'secondary' => '#06b6d4',  // cyan-500 (cool complement)
    ],

    'royal-purple' => [
        'label' => 'Royal Purple',
        'primary' => '#a855f7',    // purple-500
        'secondary' => '#14b8a6',  // teal-500 (vibrant complement)
    ],

    'rose-pink' => [
        'label' => 'Rose Pink',
        'primary' => '#ec4899',    // pink-500
        'secondary' => '#10b981',  // emerald-500 (fresh complement)
    ],

    'slate-gray' => [
        'label' => 'Slate Gray',
        'primary' => '#64748b',    // slate-500
        'secondary' => '#f59e0b',  // amber-500 (warm accent)
    ],

    'crimson-red' => [
        'label' => 'Crimson Red',
        'primary' => '#ef4444',    // red-500
        'secondary' => '#14b8a6',  // teal-500 (balanced complement)
    ],

    'amber-gold' => [
        'label' => 'Amber Gold',
        'primary' => '#f59e0b',    // amber-500
        'secondary' => '#6366f1',  // indigo-500 (rich complement)
    ],

    // New modern & pet-friendly presets
    'mint-lavender' => [
        'label' => 'Mint Lavender',
        'primary' => '#10b981',    // emerald-500 (mint)
        'secondary' => '#a78bfa',  // violet-400 (lavender)
    ],

    'coral-aqua' => [
        'label' => 'Coral Aqua',
        'primary' => '#f87171',    // red-400 (coral)
        'secondary' => '#06b6d4',  // cyan-500 (aqua)
    ],

    'navy-gold' => [
        'label' => 'Navy Gold',
        'primary' => '#1e40af',    // blue-800 (navy)
        'secondary' => '#f59e0b',  // amber-500 (gold)
    ],

    'sage-terracotta' => [
        'label' => 'Sage Terracotta',
        'primary' => '#84cc16',    // lime-500 (sage)
        'secondary' => '#ea580c',  // orange-600 (terracotta)
    ],

    'indigo-cyan' => [
        'label' => 'Indigo Cyan',
        'primary' => '#6366f1',    // indigo-500
        'secondary' => '#22d3ee',  // cyan-400
    ],

    'chocolate-cream' => [
        'label' => 'Chocolate Cream',
        'primary' => '#78350f',    // amber-900 (chocolate)
        'secondary' => '#fbbf24',  // amber-400 (cream)
    ],

    'ocean-depths' => [
        'label' => 'Ocean Depths',
        'primary' => '#0c4a6e',    // sky-900 (deep blue)
        'secondary' => '#2dd4bf',  // teal-400 (turquoise)
    ],

    'cherry-blossom' => [
        'label' => 'Cherry Blossom',
        'primary' => '#fda4af',    // rose-300 (soft pink)
        'secondary' => '#fb7185',  // rose-400 (rose accent)
    ],

    'meadow-sunrise' => [
        'label' => 'Meadow Sunrise',
        'primary' => '#22c55e',    // green-500 (meadow)
        'secondary' => '#fb923c',  // orange-400 (sunrise)
    ],

    'plum-berry' => [
        'label' => 'Plum Berry',
        'primary' => '#7c3aed',    // violet-600 (plum)
        'secondary' => '#ec4899',  // pink-500 (berry)
    ],
];
