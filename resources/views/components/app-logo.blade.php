@php
    $siteLogo = App\Models\Setting::get('site_logo');
@endphp

@if($siteLogo && Storage::disk('public')->exists($siteLogo))
    <div class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-md">
        <img src="{{ Storage::disk('public')->url($siteLogo) }}" alt="{{ App\Models\Setting::get('site_name') }}" class="size-full object-contain" />
    </div>
@else
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
        <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
    </div>
@endif

<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold">{{ App\Models\Setting::get('site_name') }}</span>
</div>
