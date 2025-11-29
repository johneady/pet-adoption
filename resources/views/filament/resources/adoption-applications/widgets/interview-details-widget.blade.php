@php
    $interview = $this->getInterview();
    $hasInterview = $this->hasInterview();
@endphp

<x-filament-widgets::widget>
    <x-filament::section :collapsible="true" :collapsed="true">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-calendar" class="h-5 w-5 text-gray-400" />
                <span>Interview :: {{ $interview?->scheduled_at->setTimezone(auth()->user()->timezone)->since() }}</span>
            </div>
        </x-slot>
        @if ($hasInterview && $interview)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Scheduled Date/Time Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                            <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Scheduled Date & Time</div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">
                                {{ $interview->scheduled_at->setTimezone(auth()->user()->timezone)->format('M d, Y \a\t h:i A') }}
                            </div>
                            <div class="mt-2">
                                @if ($interview->scheduled_at->isFuture())
                                    <x-filament::badge color="info" icon="heroicon-m-clock" size="sm">
                                        Upcoming
                                    </x-filament::badge>
                                @elseif ($interview->completed_at)
                                    <x-filament::badge color="success" icon="heroicon-m-check-circle" size="sm">
                                        Completed
                                    </x-filament::badge>
                                @else
                                    <x-filament::badge color="warning" icon="heroicon-m-exclamation-triangle" size="sm">
                                        Pending Completion
                                    </x-filament::badge>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Location Card --}}
                @if ($interview->location)
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                                <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Location</div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">
                                    {{ $interview->location }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Completed At Card --}}
                @if ($interview->completed_at)
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 size-10 rounded-lg bg-success-50 dark:bg-success-900/20 flex items-center justify-center">
                                <svg class="size-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Completed At</div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">
                                    {{ $interview->completed_at->setTimezone(auth()->user()->timezone)->format('M d, Y \a\t h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Interview Notes Card (Full Width) --}}
                @if ($interview->notes)
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 lg:col-span-2">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                                <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Interviewer Notes</div>
                                <div class="text-sm text-gray-900 dark:text-white leading-relaxed">
                                    {{ $interview->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="flex flex-col items-center gap-4 py-6">
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <x-filament::icon icon="heroicon-m-calendar" class="h-5 w-5" />
                    <span>No interview has been scheduled for this application.</span>
                </div>

                @if ($record->status === 'submitted')
                    {{ ($this->scheduleInterviewAction)(['record' => $record]) }}
                @endif
            </div>
        @endif
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
