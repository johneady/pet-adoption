@php
    $footerPages = \App\Models\Page::whereNotNull('menu_id')
        ->published()
        ->when(!auth()->check(), fn($query) => $query->where('requires_auth', false))
        ->orderBy('display_order')
        ->limit(6)
        ->get();

    $specialPages = \App\Models\Page::whereIn('slug', ['about-us', 'contact-us', 'privacy-policy', 'terms-of-service'])
        ->published()
        ->get()
        ->keyBy('slug');

    $socialLinks = [
        'facebook' => \App\Models\Setting::get('social_facebook'),
        'twitter' => \App\Models\Setting::get('social_twitter'),
        'instagram' => \App\Models\Setting::get('social_instagram'),
        'youtube' => \App\Models\Setting::get('social_youtube'),
        'linkedin' => \App\Models\Setting::get('social_linkedin'),
    ];

    $hasSocialLinks = !empty(array_filter($socialLinks));
@endphp
<footer class="mt-auto border-t-2 border-ocean-200 bg-gradient-to-r from-ocean-50 to-teal-50 dark:border-ocean-800 dark:from-ocean-950 dark:to-teal-950">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-3">
            <!-- About Section -->
            <div>
                <div class="mb-4 flex items-center space-x-2">
                    <x-app-logo />
                </div>
                <flux:text size="sm" class="text-ocean-700 dark:text-ocean-300">
                    {{ App\Models\Setting::get('site_tagline') }}
                </flux:text>
            </div>

            <!-- Quick Links -->
            <div>
                <flux:heading size="sm" class="mb-4 text-ocean-900 dark:text-ocean-100">Quick Links</flux:heading>
                <nav class="flex flex-col gap-2">
                    <a href="{{ route('pets.index') }}" class="text-sm text-ocean-700 transition-colors hover:text-ocean-900 dark:text-ocean-300 dark:hover:text-ocean-100" wire:navigate>
                        Adopt a Pet
                    </a>
                    <a href="{{ route('blog.index') }}" class="text-sm text-ocean-700 transition-colors hover:text-ocean-900 dark:text-ocean-300 dark:hover:text-ocean-100" wire:navigate>
                        Blog
                    </a>
                    @if($specialPages->has('about-us'))
                        <a href="{{ route('page.show', 'about-us') }}" class="text-sm text-ocean-700 transition-colors hover:text-ocean-900 dark:text-ocean-300 dark:hover:text-ocean-100" wire:navigate>
                            About Us
                        </a>
                    @endif
                    @if($specialPages->has('contact-us'))
                        <a href="{{ route('page.show', 'contact-us') }}" class="text-sm text-ocean-700 transition-colors hover:text-ocean-900 dark:text-ocean-300 dark:hover:text-ocean-100" wire:navigate>
                            Contact
                        </a>
                    @endif
                    @foreach($footerPages as $page)
                        <a href="{{ route('page.show', $page->slug) }}" class="text-sm text-ocean-700 transition-colors hover:text-ocean-900 dark:text-ocean-300 dark:hover:text-ocean-100" wire:navigate>
                            {{ $page->title }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Social Media & Legal -->
            <div>
                <flux:heading size="sm" class="mb-4 text-ocean-900 dark:text-ocean-100">Connect With Us</flux:heading>

                @if($hasSocialLinks)
                    <!-- Social Media Links -->
                    <div class="mb-6 flex gap-3">
                        @if($socialLinks['facebook'])
                            <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-ocean-300 bg-white text-ocean-700 transition-all hover:border-ocean-500 hover:bg-ocean-50 hover:text-ocean-900 dark:border-ocean-700 dark:bg-zinc-900 dark:text-ocean-300 dark:hover:border-ocean-500 dark:hover:bg-ocean-950 dark:hover:text-ocean-100"
                               aria-label="Facebook">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 3.667h-3.533v7.98H9.101z"/>
                                </svg>
                            </a>
                        @endif
                        @if($socialLinks['twitter'])
                            <a href="{{ $socialLinks['twitter'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-ocean-300 bg-white text-ocean-700 transition-all hover:border-ocean-500 hover:bg-ocean-50 hover:text-ocean-900 dark:border-ocean-700 dark:bg-zinc-900 dark:text-ocean-300 dark:hover:border-ocean-500 dark:hover:bg-ocean-950 dark:hover:text-ocean-100"
                               aria-label="Twitter">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                        @endif
                        @if($socialLinks['instagram'])
                            <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-ocean-300 bg-white text-ocean-700 transition-all hover:border-ocean-500 hover:bg-ocean-50 hover:text-ocean-900 dark:border-ocean-700 dark:bg-zinc-900 dark:text-ocean-300 dark:hover:border-ocean-500 dark:hover:bg-ocean-950 dark:hover:text-ocean-100"
                               aria-label="Instagram">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                </svg>
                            </a>
                        @endif
                        @if($socialLinks['youtube'])
                            <a href="{{ $socialLinks['youtube'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-ocean-300 bg-white text-ocean-700 transition-all hover:border-ocean-500 hover:bg-ocean-50 hover:text-ocean-900 dark:border-ocean-700 dark:bg-zinc-900 dark:text-ocean-300 dark:hover:border-ocean-500 dark:hover:bg-ocean-950 dark:hover:text-ocean-100"
                               aria-label="YouTube">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                        @endif
                        @if($socialLinks['linkedin'])
                            <a href="{{ $socialLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-ocean-300 bg-white text-ocean-700 transition-all hover:border-ocean-500 hover:bg-ocean-50 hover:text-ocean-900 dark:border-ocean-700 dark:bg-zinc-900 dark:text-ocean-300 dark:hover:border-ocean-500 dark:hover:bg-ocean-950 dark:hover:text-ocean-100"
                               aria-label="LinkedIn">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Legal Links -->
                <nav class="flex flex-col gap-2">
                    @if($specialPages->has('privacy-policy'))
                        <a href="{{ route('page.show', 'privacy-policy') }}" class="text-sm text-ocean-700 transition-colors hover:text-ocean-900 dark:text-ocean-300 dark:hover:text-ocean-100" wire:navigate>
                            Privacy Policy
                        </a>
                    @endif
                    @if($specialPages->has('terms-of-service'))
                        <a href="{{ route('page.show', 'terms-of-service') }}" class="text-sm text-ocean-700 transition-colors hover:text-ocean-900 dark:text-ocean-300 dark:hover:text-ocean-100" wire:navigate>
                            Terms of Service
                        </a>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Copyright -->
        <flux:separator class="my-6" />
        <div class="text-center">
            <flux:text size="sm" class="text-ocean-600 dark:text-ocean-400">
                &copy; {{ date('Y') }} {{ App\Models\Setting::get('site_name') }}. All rights reserved.
            </flux:text>
        </div>
    </div>
</footer>
