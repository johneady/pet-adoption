@php
    $pet = $this->getPet();
    $primaryPhoto = $pet?->photos->firstWhere('is_primary', true) ?? $pet?->photos->first();
    $allPhotos = $pet?->photos ?? collect();
@endphp

<x-filament-widgets::widget>
    <x-filament::section :collapsible="true" :collapsed="true">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-heart" class="h-5 w-5 text-gray-400" />
                <span>Pet Being Adopted</span>
            </div>
        </x-slot>
        @if ($pet)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Photo & Basic Info Column --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex flex-col gap-4">
                        {{-- Primary Photo --}}
                        <div class="relative">
                            <img
                                src="{{ $primaryPhoto ? Storage::disk('public')->url($primaryPhoto->file_path) : asset('images/placeholder-pet.png') }}"
                                alt="{{ $pet->name }}"
                                class="w-full h-64 rounded-lg object-cover ring-4 ring-gray-100 dark:ring-gray-700"
                                onerror="this.onerror=null; this.src='{{ asset('images/placeholder-pet.png') }}';"
                            />

                            {{-- Status Badge Overlay --}}
                            <div class="absolute top-3 right-3">
                                <x-filament::badge
                                    size="lg"
                                    :color="match ($pet->status) {
                                        'available' => 'success',
                                        'pending' => 'warning',
                                        'adopted' => 'info',
                                        default => 'gray',
                                    }"
                                >
                                    {{ ucfirst($pet->status) }}
                                </x-filament::badge>
                            </div>
                        </div>

                        {{-- Photo Gallery Thumbnails --}}
                        @if ($allPhotos->count() > 1)
                            <div class="flex gap-2 overflow-x-auto pb-2">
                                @foreach ($allPhotos->take(4) as $photo)
                                    <img
                                        src="{{ Storage::disk('public')->url($photo->file_path) }}"
                                        alt="{{ $pet->name }}"
                                        class="size-16 rounded-lg object-cover ring-2 ring-gray-200 dark:ring-gray-600 hover:ring-primary-500 transition-all cursor-pointer shrink-0"
                                        onerror="this.style.display='none'"
                                    />
                                @endforeach
                                @if ($allPhotos->count() > 4)
                                    <div class="size-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-medium text-gray-600 dark:text-gray-300 shrink-0">
                                        +{{ $allPhotos->count() - 4 }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Name & Species/Breed --}}
                        <div class="text-center border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $pet->name }}
                            </h3>
                            <div class="flex items-center justify-center gap-2 mt-2">
                                <x-filament::badge color="info" size="md">
                                    {{ $pet->species->name }}
                                </x-filament::badge>
                                @if ($pet->breed)
                                    <x-filament::badge color="gray" size="md">
                                        {{ $pet->breed->name }}
                                    </x-filament::badge>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Details Column --}}
                <div class="space-y-4">
                    {{-- Pet Details Cards --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Age --}}
                        @if ($pet->age)
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                                        <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Age</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">
                                            {{ $pet->age }} {{ Str::plural('year', $pet->age) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Gender --}}
                        @if ($pet->gender)
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                                        @if ($pet->gender === 'male')
                                            <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 3h4v4m-4-4l-6 6m6 8a6 6 0 11-12 0 6 6 0 0112 0z"/>
                                            </svg>
                                        @else
                                            <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m0 0l-3-3m3 3l3-3m3-9a6 6 0 11-12 0 6 6 0 0112 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Gender</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">
                                            {{ ucfirst($pet->gender) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Size --}}
                        @if ($pet->size)
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                                        <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Size</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">
                                            {{ ucfirst(str_replace('_', ' ', $pet->size)) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Color (if available) --}}
                        @if ($pet->color)
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                                        <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Color</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">
                                            {{ ucfirst($pet->color) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Description --}}
                    @if ($pet->description)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                                    <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">About {{ $pet->name }}</div>
                                    <div class="text-sm text-gray-900 dark:text-white leading-relaxed">
                                        {{ $pet->description }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">No pet information available.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
