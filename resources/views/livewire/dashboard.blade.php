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
            <div class="mb-8 rounded-xl border p-6"
                style="border-color: {{ Auth::user()->currentMembership->plan->badge_color }}40; background: linear-gradient(135deg, {{ Auth::user()->currentMembership->plan->badge_color }}10, transparent);">
                <div class="flex items-center gap-4">
                    <div class="rounded-lg p-3"
                        style="background-color: {{ Auth::user()->currentMembership->plan->badge_color }}20">
                        <flux:icon.star class="size-8"
                            style="color: {{ Auth::user()->currentMembership->plan->badge_color }}" />
                    </div>
                    <div class="flex-grow">
                        <flux:heading size="lg">{{ Auth::user()->currentMembership->plan->name }} Member
                        </flux:heading>
                        <flux:text>Thank you for supporting our mission! Your membership expires
                            {{ Auth::user()->currentMembership->expires_at->diffForHumans() }}.</flux:text>
                    </div>
                    <flux:button href="{{ route('membership.manage') }}" variant="outline" size="sm">
                        Manage
                    </flux:button>
                </div>
            </div>
        @else
            @if (\App\Models\Setting::get('memberships_enabled'))
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
        @endif

        @if ($userApplications->isNotEmpty())
            <div class="space-y-4">
                <flux:heading size="lg" class="mb-6">Your Adoption Applications</flux:heading>

                @foreach ($userApplications as $index => $application)
                    @php
                        $status = $applicationStatuses[$application->status] ?? null;
                        $isFirst = $index === 0;
                        $isFirst = false; // Temporarily disable auto-open feature
                    @endphp

                    <div x-data="{ open: {{ $isFirst ? 'true' : 'false' }} }"
                        class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                        <!-- Collapsible Header -->
                        <button @click="open = !open" class="flex w-full items-center justify-between p-6 text-left">
                            <div class="flex items-center gap-4">
                                @if ($application->pet->primaryPhoto)
                                    <img src="{{ Storage::url($application->pet->primaryPhoto->file_path) }}"
                                        alt="{{ $application->pet->name }}"
                                        class="size-16 rounded-lg object-cover">
                                @else
                                    <div
                                        class="flex size-16 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                        <svg class="size-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <div>
                                    <flux:heading size="md" class="mb-1">{{ $application->pet->name }}</flux:heading>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                        Applied {{ $application->created_at->diffForHumans() }}
                                    </flux:text>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                @if ($status)
                                    <div
                                        class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5
                                        @if ($status['color'] === 'green') border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-200
                                        @elseif ($status['color'] === 'blue') border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-200
                                        @elseif ($status['color'] === 'yellow') border-yellow-200 bg-yellow-50 text-yellow-800 dark:border-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200
                                        @elseif ($status['color'] === 'purple') border-purple-200 bg-purple-50 text-purple-800 dark:border-purple-800 dark:bg-purple-900/20 dark:text-purple-200
                                        @elseif ($status['color'] === 'red') border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200
                                        @else border-gray-200 bg-gray-50 text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 @endif">
                                        <span
                                            class="size-2 rounded-full
                                            @if ($status['color'] === 'green') bg-green-500
                                            @elseif ($status['color'] === 'blue') bg-blue-500
                                            @elseif ($status['color'] === 'yellow') bg-yellow-500
                                            @elseif ($status['color'] === 'purple') bg-purple-500
                                            @elseif ($status['color'] === 'red') bg-red-500
                                            @else bg-gray-500 @endif"></span>
                                        <span class="text-sm font-medium">{{ $status['label'] }}</span>
                                    </div>
                                @endif

                                <svg :class="{ 'rotate-180': open }"
                                    class="size-5 text-gray-400 transition-transform dark:text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <!-- Collapsible Content -->
                        <div x-show="open" x-collapse>
                            <div class="border-t border-gray-200 p-6 dark:border-gray-700">
                                <div class="grid gap-6 md:grid-cols-3">
                                    <!-- Pet Photo and Info -->
                                    <div class="md:col-span-1">
                                        @if ($application->pet->primaryPhoto)
                                            <img src="{{ Storage::url($application->pet->primaryPhoto->file_path) }}"
                                                alt="{{ $application->pet->name }}"
                                                class="aspect-square w-full rounded-lg object-cover">
                                        @else
                                            <div
                                                class="flex aspect-square w-full items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                                <svg class="size-16 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="mt-4">
                                            <flux:heading size="md" class="mb-1">{{ $application->pet->name }}
                                            </flux:heading>
                                            <flux:text class="text-sm">
                                                {{ $application->pet->species->name }}
                                                @if ($application->pet->breed)
                                                    &bull; {{ $application->pet->breed->name }}
                                                @endif
                                            </flux:text>
                                            @if ($application->pet->age)
                                                <flux:text class="text-sm">{{ $application->pet->age }} years old
                                                </flux:text>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Application Details -->
                                    <div class="md:col-span-2">
                                        <!-- Status Badge -->
                                        @if ($status)
                                            <flux:text class="mb-6 text-sm">{{ $status['description'] }}</flux:text>
                                        @endif

                                        <!-- Application Information -->
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <flux:text
                                                    class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                                                    Application
                                                    Submitted</flux:text>
                                                <flux:text class="text-sm">{{ $application->created_at->format('M j, Y') }}
                                                </flux:text>
                                            </div>

                                            @if ($application->interview)
                                                <div>
                                                    <flux:text
                                                        class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                                                        Interview Scheduled</flux:text>
                                                    <flux:text class="text-sm">
                                                        {{ $application->interview->scheduled_at->timezone(Auth()->user()->timezone)->format('M j, Y \a\t g:i A') }}
                                                    </flux:text>
                                                </div>
                                            @endif

                                        </div>

                                        <!-- Action Button -->
                                        <div class="mt-6">
                                            <flux:button href="{{ route('pets.show', $application->pet->slug) }}"
                                                wire:navigate variant="outline">
                                                View Pet Details
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div
                class="flex min-h-[400px] items-center justify-center rounded-xl border-2 border-ocean-200 bg-gradient-to-br from-ocean-50 to-teal-50 p-12 dark:border-ocean-800 dark:from-ocean-950 dark:to-teal-950">
                <div class="text-center">
                    <svg class="mx-auto h-24 w-24 text-ocean-300 dark:text-ocean-700" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <flux:heading size="lg" class="mb-2 mt-4 text-ocean-900 dark:text-ocean-100">
                        No Applications Yet
                    </flux:heading>
                    <flux:text class="mb-4 text-ocean-700 dark:text-ocean-300">
                        You haven't submitted any adoption applications. Ready to find your perfect companion?
                    </flux:text>
                    <flux:button href="{{ route('pets.index') }}" wire:navigate variant="primary">
                        Browse Our Pets
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</div>
