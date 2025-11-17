<section class="w-full px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('Notifications')" :subheading="__('Manage your email notification preferences')">
            <form wire:submit="updateNotificationPreferences" class="my-6 w-full space-y-6">
                @if (auth()->user()->is_admin)
                    <div class="space-y-4">
                        <flux:checkbox wire:model="receive_new_user_alerts" :label="__('New User Registrations')" :description="__('Receive email alerts when new users register on the platform')" />

                        <flux:checkbox wire:model="receive_new_adoption_alerts" :label="__('New Adoption Applications')" :description="__('Receive email alerts when new adoption applications are submitted')" />
                    </div>
                @else
                    <flux:text class="text-sm">
                        {{ __('No preferences available.') }}
                    </flux:text>
                @endif

                @if (auth()->user()->is_admin)
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-end">
                            <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                        </div>

                        <x-action-message class="me-3" on="notifications-updated">
                            {{ __('Saved.') }}
                        </x-action-message>
                    </div>
                @endif
            </form>
        </x-settings.layout>
    </div>
</section>
