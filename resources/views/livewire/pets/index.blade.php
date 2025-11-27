<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
                <!-- Header -->
                <div class="relative mb-8 overflow-hidden rounded-2xl bg-cover bg-center p-8" style="background-image: url('{{ asset('images/default_companion.jpg') }}');">
                    <div class="absolute inset-0 bg-zinc-900/45"></div>
                    <div class="relative mx-auto max-w-4xl text-center">
                        <flux:heading size="xl" class="mb-2 text-white">Find Your Perfect Companion</flux:heading>
                        <flux:text class="text-lg text-white/90">
                            Browse our available pets and find your new best friend
                        </flux:text>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
                    <!-- Filters Sidebar -->
                    <div class="space-y-4">
                        <div class="rounded-xl border-2 border-ocean-200 bg-white p-6 shadow-sm shadow-ocean-100 dark:border-ocean-800 dark:bg-zinc-900 dark:shadow-ocean-950">
                            <div class="mb-4 flex items-center justify-between">
                                <flux:heading size="lg" class="text-ocean-900 dark:text-ocean-100">Filters</flux:heading>
                                @if($search || $speciesId || $breedId || $gender || $size || $minAge || $maxAge)
                                    <flux:button wire:click="clearFilters" variant="ghost" size="sm">Clear</flux:button>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <!-- Search -->
                                <div>
                                    <flux:field>
                                        <flux:label>Search by name</flux:label>
                                        <flux:input wire:model.live.debounce.300ms="search" placeholder="Enter pet name..." />
                                    </flux:field>
                                </div>

                                <!-- Species -->
                                <div>
                                    <flux:field>
                                        <flux:label>Species</flux:label>
                                        <flux:select wire:model.live="speciesId">
                                            <option value="">All species</option>
                                            @foreach($species as $spec)
                                                <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                                            @endforeach
                                        </flux:select>
                                    </flux:field>
                                </div>

                                <!-- Breed -->
                                @if($speciesId && $breeds->isNotEmpty())
                                    <div>
                                        <flux:field>
                                            <flux:label>Breed</flux:label>
                                            <flux:select wire:model.live="breedId">
                                                <option value="">All breeds</option>
                                                @foreach($breeds as $breed)
                                                    <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                                                @endforeach
                                            </flux:select>
                                        </flux:field>
                                    </div>
                                @endif

                                <!-- Gender -->
                                <div>
                                    <flux:field>
                                        <flux:label>Gender</flux:label>
                                        <flux:select wire:model.live="gender">
                                            <option value="">Any gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </flux:select>
                                    </flux:field>
                                </div>

                                <!-- Size -->
                                <div>
                                    <flux:field>
                                        <flux:label>Size</flux:label>
                                        <flux:select wire:model.live="size">
                                            <option value="">Any size</option>
                                            <option value="small">Small</option>
                                            <option value="medium">Medium</option>
                                            <option value="large">Large</option>
                                            <option value="extra_large">Extra Large</option>
                                        </flux:select>
                                    </flux:field>
                                </div>

                                <!-- Age Range -->
                                <div class="space-y-2">
                                    <flux:label>Age Range (years)</flux:label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <flux:field>
                                            <flux:input type="number" wire:model.live="minAge" placeholder="Min" min="0" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:input type="number" wire:model.live="maxAge" placeholder="Max" min="0" />
                                        </flux:field>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pets Grid -->
                    <div>
                        <div wire:loading.class="opacity-50 transition-opacity" class="min-h-screen">
                            @if($pets->count() > 0)
                                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach($pets as $pet)
                                        <a href="{{ route('pets.show', $pet->slug) }}" wire:navigate
                                           class="group overflow-hidden rounded-xl border-2 border-ocean-200 bg-white transition-all hover:border-ocean-400 hover:shadow-lg hover:shadow-ocean-200/50 dark:border-ocean-800 dark:bg-zinc-900 dark:hover:border-ocean-600 dark:hover:shadow-ocean-900/50">
                                            <!-- Pet Image -->
                                            <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                                @php
                                                    $primaryPhoto = $pet->photos->firstWhere('is_primary', true) ?? $pet->photos->first();
                                                @endphp
                                                @if($primaryPhoto)
                                                    <img src="{{ Storage::disk('public')->url($primaryPhoto->file_path) }}"
                                                         alt="{{ $pet->name }}"
                                                         class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                                         onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                                        <svg class="h-24 w-24 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                                        <svg class="h-24 w-24 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif

                                                <!-- Status Badge -->
                                                <div class="absolute left-3 top-3">
                                                    <flux:badge variant="success" size="sm" class="border-teal-600 bg-teal-500 backdrop-blur-sm">Available</flux:badge>
                                                </div>
                                            </div>

                                            <!-- Pet Info -->
                                            <div class="p-4">
                                                <div class="mb-2 flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <flux:heading size="lg" class="mb-1">{{ $pet->name }}</flux:heading>
                                                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                                                            {{ $pet->breed?->name ?? $pet->species->name }}
                                                        </flux:text>
                                                    </div>
                                                </div>

                                                <!-- Pet Details -->
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @if($pet->age)
                                                        <flux:badge variant="outline" size="sm">
                                                            {{ $pet->age }} {{ Str::plural('year', $pet->age) }}
                                                        </flux:badge>
                                                    @endif
                                                    @if($pet->gender)
                                                        <flux:badge variant="outline" size="sm">
                                                            {{ ucfirst($pet->gender) }}
                                                        </flux:badge>
                                                    @endif
                                                    @if($pet->size)
                                                        <flux:badge variant="outline" size="sm">
                                                            {{ ucfirst(str_replace('_', ' ', $pet->size)) }}
                                                        </flux:badge>
                                                    @endif
                                                </div>

                                                <!-- Description Preview -->
                                                @if($pet->description)
                                                    <flux:text size="sm" class="mt-3 line-clamp-2 text-zinc-600 dark:text-zinc-400">
                                                        {{ $pet->description }}
                                                    </flux:text>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                <div class="mt-8">
                                    {{ $pets->links() }}
                                </div>
                            @else
                                <!-- Empty State -->
                                <div class="flex min-h-[400px] items-center justify-center rounded-xl border-2 border-ocean-200 bg-gradient-to-br from-ocean-50 to-teal-50 p-12 dark:border-ocean-800 dark:from-ocean-950 dark:to-zinc-900">
                                    <div class="text-center">
                                        <svg class="mx-auto h-24 w-24 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <flux:heading size="lg" class="mb-2 mt-4 text-ocean-900 dark:text-ocean-100">No pets found</flux:heading>
                                        <flux:text class="mb-4 text-ocean-700 dark:text-ocean-300">
                                            Try adjusting your filters to see more results
                                        </flux:text>
                                        @if($search || $speciesId || $breedId || $gender || $size || $minAge || $maxAge)
                                            <flux:button wire:click="clearFilters" variant="primary">Clear all filters</flux:button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Loading Indicator -->
                        <div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-black/5 backdrop-blur-sm">
                            <div class="rounded-lg border-2 border-ocean-300 bg-white px-6 py-4 shadow-lg shadow-ocean-200/50 dark:border-ocean-700 dark:bg-zinc-900 dark:shadow-ocean-900/50">
                                <div class="flex items-center gap-3">
                                    <svg class="h-5 w-5 animate-spin text-ocean-600 dark:text-ocean-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <flux:text class="text-ocean-700 dark:text-ocean-300">Loading...</flux:text>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
    </div>
</div>
