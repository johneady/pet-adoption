<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <flux:heading size="xl" class="mb-2">Dashboard</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">Welcome back, {{ Auth::user()->name }}!</flux:text>
        </div>

        @if (session('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @mouseenter="clearTimeout($root._x_timeout)"
                @mouseleave="$root._x_timeout = setTimeout(() => show = false, 2000)"
                class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                <flux:text class="text-green-800 dark:text-green-200">
                    {{ session('message') }}
                </flux:text>
            </div>
        @endif

        @if (Auth::user()->hasActiveMembership())
            <div class="mb-8 rounded-xl border p-6" style="border-color: {{ Auth::user()->currentMembership->plan->badge_color }}40; background: linear-gradient(135deg, {{ Auth::user()->currentMembership->plan->badge_color }}10, transparent);">
                <div class="flex items-center gap-4">
                    <div class="rounded-lg p-3" style="background-color: {{ Auth::user()->currentMembership->plan->badge_color }}20">
                        <flux:icon.star class="size-8" style="color: {{ Auth::user()->currentMembership->plan->badge_color }}" />
                    </div>
                    <div class="flex-grow">
                        <flux:heading size="lg">{{ Auth::user()->currentMembership->plan->name }} Member</flux:heading>
                        <flux:text>Thank you for supporting our mission! Your membership expires {{ Auth::user()->currentMembership->expires_at->diffForHumans() }}.</flux:text>
                    </div>
                    <flux:button href="{{ route('membership.manage') }}" variant="outline" size="sm">
                        Manage
                    </flux:button>
                </div>
            </div>
        @else
            <div class="mb-8 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg" class="mb-1">Support Our Mission</flux:heading>
                        <flux:text>Consider becoming a member to help us provide better care for pets.</flux:text>
                    </div>
                    <flux:button href="{{ route('membership.plans') }}" variant="primary">
                        View Plans
                    </flux:button>
                </div>
            </div>
        @endif

            <div
                class="flex min-h-[400px] items-center justify-center rounded-xl border-2 border-ocean-200 bg-gradient-to-br from-ocean-50 to-teal-50 p-12 dark:border-ocean-800 dark:from-ocean-950 dark:to-teal-950">
                <div class="text-center">
                    <svg class="mx-auto h-24 w-24 text-ocean-300 dark:text-ocean-700" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <flux:heading size="lg" class="mb-2 mt-4 text-ocean-900 dark:text-ocean-100">No Applications Yet</flux:heading>
                    <flux:text class="mb-4 text-ocean-700 dark:text-ocean-300">
                        You haven't submitted any adoption applications. Ready to find your perfect companion?
                    </flux:text>
                    <flux:button href="{{ route('pets.index') }}" wire:navigate variant="primary">
                        Browse Our Pets
                    </flux:button>
                </div>
            </div>
    </div>
</div>
