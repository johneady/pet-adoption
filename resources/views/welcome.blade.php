<x-layouts.app :title="__('Find Your Perfect Companion')">
    {{-- Hero Section --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-teal-950">
        <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight text-ocean-900 dark:text-ocean-50 sm:text-6xl">
                    Find Your Perfect Companion
                </h1>
                <p class="mt-6 text-lg leading-8 text-ocean-700 dark:text-ocean-200">
                    Every pet deserves a loving home. Browse our available pets and start your journey to finding a new
                    family member today.
                </p>
                <div class="mt-10 flex items-center justify-center gap-6">
                    <flux:button variant="primary" href="{{ route('home') }}" class="text-lg px-8 py-3">
                        Browse Available Pets
                    </flux:button>
                    @guest
                        <flux:button href="{{ route('login') }}" class="text-lg px-8 py-3">
                            Sign In
                        </flux:button>
                    @endguest
                </div>
            </div>
        </div>

        {{-- Decorative background elements --}}
        <div class="absolute left-1/2 top-0 -z-10 -translate-x-1/2 blur-3xl xl:-top-6" aria-hidden="true">
            <div class="aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-ocean-200 to-teal-200 dark:from-ocean-900 dark:to-teal-900 opacity-30"
                style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
            </div>
        </div>
    </div>

    {{-- Stats Section --}}
    <div class="bg-white dark:bg-gray-900 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-3 text-center">
                <div>
                    <div class="text-4xl font-bold text-ocean-600 dark:text-ocean-400">50+</div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Pets Adopted</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-teal-600 dark:text-teal-400">50+</div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Available Now</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-ocean-700 dark:text-ocean-300">100%</div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Love Guaranteed</div>
                </div>
            </div>
        </div>
    </div>

    {{-- How It Works Section --}}
    <div class="bg-gray-50 dark:bg-gray-800 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    How Adoption Works
                </h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                    Simple steps to find your new best friend
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                {{-- Step 1 --}}
                <div class="relative">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-ocean-600 text-white font-bold text-xl mb-4">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        Browse Pets
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Explore our collection of lovable pets waiting for their forever homes. Filter by species, age,
                        size, and more.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="relative">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-teal-600 text-white font-bold text-xl mb-4">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        Apply Online
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Found the perfect match? Submit your adoption application online. Our team will review it
                        promptly.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="relative">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-ocean-700 text-white font-bold text-xl mb-4">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        Bring Them Home
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Once approved, schedule a meet and greet. When you're ready, complete the adoption and welcome
                        your new family member!
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Why Adopt Section --}}
    <div class="bg-white dark:bg-gray-900 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 items-center">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl mb-6">
                        Why Adopt From Us?
                    </h2>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400 shrink-0 mt-1" />
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Health Checked</h3>
                                <p class="text-gray-600 dark:text-gray-400">All pets are vaccinated, spayed/neutered,
                                    and health certified.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400 shrink-0 mt-1" />
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Ongoing Support</h3>
                                <p class="text-gray-600 dark:text-gray-400">We provide lifetime support and guidance
                                    for all adopters.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400 shrink-0 mt-1" />
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Perfect Match</h3>
                                <p class="text-gray-600 dark:text-gray-400">Our team helps ensure the best fit for
                                    your lifestyle and home.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400 shrink-0 mt-1" />
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Save a Life</h3>
                                <p class="text-gray-600 dark:text-gray-400">Give a deserving pet a second chance at
                                    happiness.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="aspect-square rounded-2xl bg-gradient-to-br from-ocean-100 to-teal-100 dark:from-ocean-900/30 dark:to-teal-900/30 flex items-center justify-center">
                        <svg class="w-48 h-48 text-ocean-400 dark:text-ocean-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA Section --}}
    <div class="bg-gradient-to-r from-ocean-600 to-teal-600 dark:from-ocean-800 dark:to-teal-800">
        <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                    Ready to Meet Your New Best Friend?
                </h2>
                <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-ocean-100">
                    Start your adoption journey today. Browse our available pets and find the perfect companion for your
                    family.
                </p>
                <div class="mt-10 flex items-center justify-center gap-6">
                    <flux:button href="{{ route('home') }}"
                        class="bg-white text-ocean-700 hover:bg-gray-100 dark:bg-gray-100 dark:text-ocean-800 dark:hover:bg-white text-lg px-8 py-3">
                        Start Browsing
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer Section --}}
    <div class="bg-gray-900 dark:bg-black">
        <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
            <div class="text-center">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} Pet Adoption Agency. Making tails wag and hearts happy.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
