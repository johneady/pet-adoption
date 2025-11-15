@php
    use App\Services\ThemeService;

    $themeService = app(ThemeService::class);
    $themeCSS = $themeService->getCachedThemeCSS();
@endphp

<style>
{!! $themeCSS !!}
</style>
