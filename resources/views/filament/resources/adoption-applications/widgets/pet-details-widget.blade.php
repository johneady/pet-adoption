@php
    $pet = $this->getPet();
    $primaryPhoto = $pet?->photos->firstWhere('is_primary', true) ?? $pet?->photos->first();
@endphp

<x-filament-widgets::widget>
    <x-filament::section heading="Pet Details" description="Information about the pet being adopted" :collapsible="true"
        :collapsed="false">
        @if ($pet)
            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="shrink-0">
                        @if ($primaryPhoto)
                            <img src="{{ Storage::disk('public')->url($primaryPhoto->file_path) }}"
                                alt="{{ $pet->name }}" class="h-24 w-24 rounded-lg object-cover" />
                        @else
                            <div
                                class="flex h-24 w-24 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                                <x-filament::icon icon="heroicon-o-photo" class="h-12 w-12 text-gray-400" />
                            </div>
                        @endif
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

                <table>
                    @if ($pet->age)
                        <tr>
                            <td class="text-sm font-semibold text-gray-700 dark:text-gray-300">Age:</td>
                            <td class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ $pet->age }} {{ Str::plural('year', $pet->age) }}
                            </td>
                        </tr>
                    @endif

                    @if ($pet->gender)
                        <tr>
                            <td class="text-sm font-semibold text-gray-700 dark:text-gray-300">Gender:</td>
                            <td>
                                <x-filament::badge class="ml-2" :color="match ($pet->gender) {
                                    'male' => 'info',
                                    'female' => 'danger',
                                    default => 'gray',
                                }">
                                    {{ ucfirst($pet->gender) }}
                                </x-filament::badge>
                            </td>
                        </tr>
                    @endif

                    @if ($pet->size)
                        <tr>
                            <td class="text-sm font-semibold text-gray-700 dark:text-gray-300">Size:</td>
                            <td>
                                <x-filament::badge class="ml-2">
                                    {{ ucfirst(str_replace('_', ' ', $pet->size)) }}
                                </x-filament::badge>
                            </td </tr>
                    @endif

                    <tr>
                        <td class="text-sm font-semibold text-gray-700 dark:text-gray-300">Status:</td>
                        <td>

                            <x-filament::badge class="ml-2" :color="match ($pet->status) {
                                'available' => 'success',
                                'pending' => 'warning',
                                'adopted' => 'info',
                                default => 'gray',
                            }">
                                {{ ucfirst($pet->status) }}
                            </x-filament::badge>
                        </td>
                    </tr>
                    @if ($pet->description)
                        <tr>
                        <tr>
                            <td class="text-sm font-semibold text-gray-700 dark:text-gray-300">Description:</td>
                            <td class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $pet->description }}</td>
                        </tr>
                        </tr>
                    @endif
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">No pet information available.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
