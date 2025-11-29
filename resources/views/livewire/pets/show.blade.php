<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <flux:breadcrumbs>
                        <flux:breadcrumbs.item href="/" wire:navigate>Home</flux:breadcrumbs.item>
                        <flux:breadcrumbs.item href="{{ route('pets.index') }}" wire:navigate>Pets</flux:breadcrumbs.item>
                        <flux:breadcrumbs.item>{{ $pet->name }}</flux:breadcrumbs.item>
                    </flux:breadcrumbs>
                </div>

                <div class="grid gap-8 lg:grid-cols-2">
                    <!-- Photo Gallery -->
                    <div class="space-y-4">
                        <!-- Main Photo -->
                        <div class="relative aspect-square overflow-hidden rounded-xl border-2 border-ocean-200 bg-gradient-to-br from-ocean-50 to-teal-50 dark:border-ocean-800 dark:from-ocean-950 dark:to-zinc-800">
                            @if($pet->photos->isNotEmpty())
                                @php
                                    $currentPhoto = $pet->photos[$selectedPhotoIndex] ?? $pet->photos->first();
                                @endphp
                                <img src="{{ Storage::disk('public')->url($currentPhoto->file_path) }}"
                                     alt="{{ $pet->name }}"
                                     class="h-full w-full object-cover"
                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                    <svg class="h-32 w-32 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <!-- Navigation Arrows -->
                                @if($pet->photos->count() > 1)
                                    <button wire:click="previousPhoto"
                                            class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full border-2 border-ocean-300 bg-white/90 p-2 shadow-lg transition-all hover:border-ocean-500 hover:bg-white dark:border-ocean-700 dark:bg-zinc-900/90 dark:hover:border-ocean-500 dark:hover:bg-zinc-900">
                                        <svg class="h-6 w-6 text-ocean-700 dark:text-ocean-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button wire:click="nextPhoto"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full border-2 border-ocean-300 bg-white/90 p-2 shadow-lg transition-all hover:border-ocean-500 hover:bg-white dark:border-ocean-700 dark:bg-zinc-900/90 dark:hover:border-ocean-500 dark:hover:bg-zinc-900">
                                        <svg class="h-6 w-6 text-ocean-700 dark:text-ocean-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif

                                <!-- Status Badge -->
                                <div class="absolute left-4 top-4">
                                    <flux:badge
                                        :variant="match ($pet->status) {
                                            'available' => 'success',
                                            'pending' => 'warning',
                                            'adopted' => 'info',
                                            'coming_soon' => 'outline',
                                            default => 'outline',
                                        }"
                                        size="lg"
                                        class="backdrop-blur-sm"
                                    >
                                        {{ ucfirst(str_replace('_', ' ', $pet->status)) }}
                                    </flux:badge>
                                </div>
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <svg class="h-32 w-32 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Thumbnail Grid -->
                        @if($pet->photos->count() > 1)
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($pet->photos as $index => $photo)
                                    <button wire:click="selectPhoto({{ $index }})"
                                            class="relative aspect-square overflow-hidden rounded-lg border-2 transition-all {{ $selectedPhotoIndex === $index ? 'border-ocean-600 ring-2 ring-ocean-600 ring-offset-2 dark:border-ocean-400 dark:ring-ocean-400 dark:ring-offset-zinc-800' : 'border-ocean-200 hover:border-ocean-400 dark:border-ocean-700 dark:hover:border-ocean-600' }}">
                                        <img src="{{ Storage::disk('public')->url($photo->file_path) }}"
                                             alt="{{ $pet->name }}"
                                             class="h-full w-full object-cover"
                                             onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                            <svg class="h-8 w-8 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Pet Information -->
                    <div class="space-y-6">
                        <!-- Header -->
                        <div>
                            <flux:heading size="2xl" class="mb-2">{{ $pet->name }}</flux:heading>
                            <flux:text size="lg" class="text-zinc-600 dark:text-zinc-400">
                                {{ $pet->breed?->name ?? $pet->species->name }}
                            </flux:text>
                        </div>

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            @if($pet->age)
                                <div class="rounded-lg border-2 border-ocean-200 bg-white p-4 dark:border-ocean-800 dark:bg-zinc-900">
                                    <flux:text size="sm" class="mb-1 text-ocean-600 dark:text-ocean-400">Age</flux:text>
                                    <flux:text class="font-semibold text-ocean-900 dark:text-ocean-100">{{ $pet->age }} {{ Str::plural('year', $pet->age) }}</flux:text>
                                </div>
                            @endif

                            @if($pet->gender)
                                <div class="rounded-lg border-2 border-ocean-200 bg-white p-4 dark:border-ocean-800 dark:bg-zinc-900">
                                    <flux:text size="sm" class="mb-1 text-ocean-600 dark:text-ocean-400">Gender</flux:text>
                                    <flux:text class="font-semibold text-ocean-900 dark:text-ocean-100">{{ ucfirst($pet->gender) }}</flux:text>
                                </div>
                            @endif

                            @if($pet->size)
                                <div class="rounded-lg border-2 border-ocean-200 bg-white p-4 dark:border-ocean-800 dark:bg-zinc-900">
                                    <flux:text size="sm" class="mb-1 text-ocean-600 dark:text-ocean-400">Size</flux:text>
                                    <flux:text class="font-semibold text-ocean-900 dark:text-ocean-100">{{ ucfirst(str_replace('_', ' ', $pet->size)) }}</flux:text>
                                </div>
                            @endif

                            @if($pet->color)
                                <div class="rounded-lg border-2 border-ocean-200 bg-white p-4 dark:border-ocean-800 dark:bg-zinc-900">
                                    <flux:text size="sm" class="mb-1 text-ocean-600 dark:text-ocean-400">Color</flux:text>
                                    <flux:text class="font-semibold text-ocean-900 dark:text-ocean-100">{{ $pet->color }}</flux:text>
                                </div>
                            @endif
                        </div>

                        <!-- Description -->
                        @if($pet->description)
                            <div class="rounded-xl border-2 border-ocean-200 bg-white p-6 dark:border-ocean-800 dark:bg-zinc-900">
                                <flux:heading size="lg" class="mb-3 text-ocean-900 dark:text-ocean-100">About {{ $pet->name }}</flux:heading>
                                <flux:text class="whitespace-pre-line text-zinc-700 dark:text-zinc-300">{{ $pet->description }}</flux:text>
                            </div>
                        @endif

                        <!-- Health & Characteristics -->
                        <div class="rounded-xl border-2 border-ocean-200 bg-white p-6 dark:border-ocean-800 dark:bg-zinc-900">
                            <flux:heading size="lg" class="mb-4 text-ocean-900 dark:text-ocean-100">Health & Characteristics</flux:heading>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <flux:text>Vaccination Status</flux:text>
                                    <flux:badge :variant="$pet->vaccination_status ? 'success' : 'warning'">
                                        {{ $pet->vaccination_status ? 'Up to date' : 'Pending' }}
                                    </flux:badge>
                                </div>
                                <div class="flex items-center justify-between">
                                    <flux:text>Special Needs</flux:text>
                                    <flux:badge :variant="$pet->special_needs ? 'warning' : 'outline'">
                                        {{ $pet->special_needs ? 'Yes' : 'No' }}
                                    </flux:badge>
                                </div>
                                <div class="flex items-center justify-between">
                                    <flux:text>Intake Date</flux:text>
                                    <flux:text class="font-semibold">{{ $pet->intake_date->format('M d, Y') }}</flux:text>
                                </div>
                            </div>

                            @if($pet->medical_notes)
                                <div class="mt-4 rounded-lg border-2 border-ocean-200 bg-ocean-50 p-4 dark:border-ocean-700 dark:bg-ocean-950/30">
                                    <flux:text size="sm" class="mb-1 font-semibold text-ocean-900 dark:text-ocean-100">Medical Notes</flux:text>
                                    <flux:text size="sm" class="text-ocean-700 dark:text-ocean-300">{{ $pet->medical_notes }}</flux:text>
                                </div>
                            @endif
                        </div>

                        <!-- Apply Button -->
                        <div class="rounded-xl border-2 border-ocean-300 bg-gradient-to-br from-ocean-50 to-teal-50 p-6 shadow-lg shadow-ocean-200/30 dark:border-ocean-700 dark:from-ocean-950/50 dark:to-teal-950/50 dark:shadow-ocean-900/30">
                            <flux:heading size="lg" class="mb-2 text-ocean-900 dark:text-ocean-100">Ready to Adopt {{ $pet->name }}?</flux:heading>
                            <flux:text class="mb-4 text-ocean-700 dark:text-ocean-300">
                                Start your adoption journey today. You'll need to create an account or sign in to continue.
                            </flux:text>

                            @auth
                                <flux:button href="{{ route('applications.create', ['petId' => $pet->id]) }}" wire:navigate variant="primary" class="w-full">
                                    Apply to Adopt {{ $pet->name }}
                                </flux:button>
                            @else
                                <div class="flex gap-3">
                                    <flux:button href="{{ route('login') }}" variant="primary" class="flex-1">
                                        Sign In to Apply
                                    </flux:button>
                                    <flux:button href="{{ route('register') }}" variant="outline" class="flex-1">
                                        Create Account
                                    </flux:button>
                                </div>
                            @endauth
                        </div>

                        <!-- Back to Listings -->
                        <div class="pt-4">
                            <flux:button href="{{ route('pets.index') }}" variant="ghost" wire:navigate icon="arrow-left">
                                Back to all pets
                            </flux:button>
                        </div>
                    </div>
            </div>
    </div>
</div>
