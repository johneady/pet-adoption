<section class="w-full px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
            </flux:radio.group>
        </x-settings.layout>
    </div>
</section>
