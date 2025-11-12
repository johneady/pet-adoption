<x-layouts.app.header :title="$title ?? null">
    <flux:main class="flex flex-col flex-1">
        <div class="flex-1">
            {{ $slot }}
        </div>
        <x-layouts.app.footer />
    </flux:main>
</x-layouts.app.header>
