@php
    $pet = $this->getPet();
    $primaryPhoto = $pet?->photos->firstWhere('is_primary', true) ?? $pet?->photos->first();
@endphp

<x-filament-widgets::widget>
    <x-filament::section heading="{{ $pet->name }}" description="Information about the pet being adopted" :collapsible="true"
        :collapsed="true">
        @if ($pet)
            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="shrink-0">
                        <img src="{{ $primaryPhoto ? Storage::disk('public')->url($primaryPhoto->file_path) : asset('images/placeholder-pet.png') }}"
                            alt="{{ $pet->name }}"
                            class="h-24 w-24 rounded-lg object-cover"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder-pet.png') }}';" />
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-semibold">{{ $pet->name }}</h3>
                        <div class="mt-1 flex flex-wrap gap-2">
                            <x-filament::badge color="info">
                                {{ $pet->species->name }}
                            </x-filament::badge>
                            @if ($pet->breed)
                                <x-filament::badge color="gray">
                                    {{ $pet->breed->name }}
                                </x-filament::badge>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-4 gap-4">
                        @if ($pet->age)
                            <div class="flex flex-col gap-1.5">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Age</dt>
                                <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $pet->age }} {{ Str::plural('year', $pet->age) }}
                                </dd>
                            </div>
                        @endif

                        @if ($pet->gender)
                            <div class="flex flex-col gap-1.5">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gender</dt>
                                <dd>
                                    <x-filament::badge :color="match ($pet->gender) {
                                        'male' => 'info',
                                        'female' => 'danger',
                                        default => 'gray',
                                    }">
                                        {{ ucfirst($pet->gender) }}
                                    </x-filament::badge>
                                </dd>
                            </div>
                        @endif

                        @if ($pet->size)
                            <div class="flex flex-col gap-1.5">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Size</dt>
                                <dd>
                                    <x-filament::badge>
                                        {{ ucfirst(str_replace('_', ' ', $pet->size)) }}
                                    </x-filament::badge>
                                </dd>
                            </div>
                        @endif

                        <div class="flex flex-col gap-1.5">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                <x-filament::badge :color="match ($pet->status) {
                                    'available' => 'success',
                                    'pending' => 'warning',
                                    'adopted' => 'info',
                                    default => 'gray',
                                }">
                                    {{ ucfirst($pet->status) }}
                                </x-filament::badge>
                            </dd>
                        </div>
                    </div>

                    @if ($pet->description)
                        <div class="flex flex-col gap-1.5">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="text-base text-gray-900 dark:text-gray-100">{{ $pet->description }}</dd>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">No pet information available.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
