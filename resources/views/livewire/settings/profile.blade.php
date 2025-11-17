<section class="w-full px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        @if (!auth()->user()->hasCompletedProfileForAdoption())
            <flux:callout variant="warning" class="mb-6">
                <strong>{{ __('Complete your profile') }}</strong>
                {{ __('Please add your phone number and address to submit adoption applications.') }}
            </flux:callout>
        @endif

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            {{-- Profile Picture --}}
            <div>
                <flux:text class="mb-2 font-medium">{{ __('Profile Picture') }}</flux:text>
                <div class="flex items-center gap-4">
                    @if (auth()->user()->profile_picture && !$removeProfilePicture)
                        <img src="{{ auth()->user()->profilePictureUrl() }}" alt="{{ auth()->user()->name }}" class="size-24 rounded-full object-cover">
                    @elseif ($profilePicture && $profilePicture->getMimeType() && str_starts_with($profilePicture->getMimeType(), 'image/'))
                        <img src="{{ $profilePicture->temporaryUrl() }}" alt="Preview" class="size-24 rounded-full object-cover">
                    @else
                        <img src="{{ url('/images/default-avatar.svg') }}" alt="{{ auth()->user()->name }}" class="size-24 rounded-full object-cover">
                    @endif

                    <div class="flex flex-col gap-2">
                        <input type="file" wire:model="profilePicture" accept="image/*" class="text-sm file:mr-4 file:rounded file:border-0 file:bg-ocean-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-ocean-700 hover:file:bg-ocean-100 dark:file:bg-ocean-900 dark:file:text-ocean-300 dark:hover:file:bg-ocean-800">

                        @if (auth()->user()->profile_picture && !$removeProfilePicture)
                            <flux:button wire:click="$set('removeProfilePicture', true)" variant="ghost" size="sm" type="button">
                                {{ __('Remove Photo') }}
                            </flux:button>
                        @endif

                        @if ($removeProfilePicture)
                            <flux:button wire:click="$set('removeProfilePicture', false)" variant="ghost" size="sm" type="button">
                                {{ __('Cancel Removal') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
                @error('profilePicture') <flux:text variant="danger" class="mt-2">{{ $message }}</flux:text> @enderror
                <div wire:loading wire:target="profilePicture" class="mt-2 text-sm text-ocean-600 dark:text-ocean-400">{{ __('Uploading...') }}</div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <flux:input wire:model="phone" :label="__('Phone Number')" type="text" autocomplete="tel" :placeholder="__('Optional - Required for adoption applications')" />

            <flux:input wire:model="address" :label="__('Address')" type="text" autocomplete="street-address" :placeholder="__('Optional - Required for adoption applications')" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
    </div>
</section>
