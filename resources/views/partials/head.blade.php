<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="keywords" content="{{ App\Models\Setting::get('seo_keywords') }}">
<meta name="description" content="{{ App\Models\Setting::get('seo_description') }}">
<meta name="author" content="Power PHP Scripts">

<title>{{ $title ?? App\Models\Setting::get('site_name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

{{-- Inline blocking script to prevent dark mode flash/alternation --}}
<script>
    (function() {
        try {
            const appearance = window.localStorage.getItem('flux.appearance');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            // Apply dark class based on stored preference or system preference
            if (appearance === 'dark' || (appearance !== 'light' && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {
            // Fallback to system preference if localStorage fails
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            }
        }
    })();
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
<x-theme-styles />
@fluxAppearance
