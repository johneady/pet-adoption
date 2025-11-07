<x-filament::section>

    <x-slot name="description">
        Contact details for {{ $adoptionApplication->user->name }}

        <div>
            <div class="text-base">
                {{ $adoptionApplication->user->name }} - {{ $adoptionApplication->user->email }}
                @if ($adoptionApplication->user->email_verified_at)
                    <x-filament::badge color="success">
                        Verified
                    </x-filament::badge>
                @else
                    <x-filament::badge color="gray">
                        Not Verified
                    </x-filament::badge>
                @endif
            </div>
            <div>
                <div class="fi-section-header-description">Member Since</div>
                <div class="text-base">
                    {{ $adoptionApplication->user->created_at->format('F j, Y') }}
                </div>
            </div>
        </div>
    </x-slot>

</x-filament::section>
