@php
    $interview = $this->getInterview();
    $hasInterview = $this->hasInterview();
@endphp

<x-filament-widgets::widget>
    <x-filament::section heading="Interview Details" description="Information about the scheduled interview"
        :collapsible="true" :collapsed="true">
        @if ($hasInterview && $interview)
            <div class="space-y-6">
                <div class="flex flex-col gap-1.5">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Interview Date/Time</dt>
                    <dd class="flex items-center gap-2">
                        <span class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            {{ $interview->scheduled_at->format('M d, Y \a\t h:i A') }}
                        </span>
                        @if ($interview->scheduled_at->isFuture())
                            <x-filament::badge color="info" icon="heroicon-m-clock">
                                Upcoming
                            </x-filament::badge>
                        @elseif ($interview->completed_at)
                            <x-filament::badge color="success" icon="heroicon-m-check-circle">
                                Completed
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="warning" icon="heroicon-m-exclamation-triangle">
                                Pending Completion
                            </x-filament::badge>
                        @endif
                    </dd>
                </div>

                @if ($interview->location)
                    <div class="flex flex-col gap-1.5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                        <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $interview->location }}
                        </dd>
                    </div>
                @endif

                @if ($interview->notes)
                    <div class="flex flex-col gap-1.5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Interviewer Notes</dt>
                        <dd class="text-base text-gray-900 dark:text-gray-100">{{ $interview->notes }}</dd>
                    </div>
                @endif

                @if ($interview->completed_at)
                    <div class="flex flex-col gap-1.5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</dt>
                        <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            {{ $interview->completed_at->format('M d, Y \a\t h:i A') }}
                        </dd>
                    </div>
                @endif
            </div>
        @else
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <x-filament::icon icon="heroicon-m-calendar" class="h-5 w-5" />
                <span>No interview has been scheduled for this application.</span>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
