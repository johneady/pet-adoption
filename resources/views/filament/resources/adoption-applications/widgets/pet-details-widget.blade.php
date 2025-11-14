@php
    $pet = $this->getPet();
    $primaryPhoto = $pet?->photos->firstWhere('is_primary', true) ?? $pet?->photos->first();
@endphp

<x-filament-widgets::widget>
    <x-filament::section heading="Pet Details" description="Information about the pet being adopted" :collapsible="true"
        :collapsed="false">
        @if ($pet)
            <table class="px-3 py-3">
                <div>
                    @if ($primaryPhoto)
                        <img src="{{ Storage::disk('public')->url($primaryPhoto->file_path) }}"
                            alt="{{ $pet->name }}" />
                    @else
                        <x-filament::icon icon="heroicon-o-photo" size="xl" />
                    @endif
                </div>

                <div>
                    <h3>{{ $pet->name }}</h3>
                    <div>
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

                @if ($pet->age)
                    <tr>
                        <td class="fi-section-header-heading font-semibold">Age:</td>
                        <td>{{ $pet->age }} {{ Str::plural('year', $pet->age) }}</td>
                    </tr>
                @endif

                @if ($pet->gender)
                    <tr>
                        <td class="fi-section-header-heading font-semibold">Gender:</td>
                        <td>
                            <x-filament::badge :color="match ($pet->gender) {
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
                        <td class="fi-section-header-heading font-semibold">Size:</td>
                        <td>
                            <x-filament::badge>
                                {{ ucfirst(str_replace('_', ' ', $pet->size)) }}
                            </x-filament::badge>
                        </td>
                    </tr>
                @endif

                <tr>
                    <td class="fi-section-header-heading font-semibold">Status:</td>
                    <td>
                        <x-filament::badge :color="match ($pet->status) {
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
                        <td class="fi-section-header-heading font-semibold">Description:</td>
                        <td>{{ $pet->description }}</td>
                    </tr>
                @endif
            </table>
        @else
            <p>No pet information available.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
