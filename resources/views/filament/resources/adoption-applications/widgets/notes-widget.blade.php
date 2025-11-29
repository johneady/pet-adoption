<x-filament-widgets::widget>
    <x-filament::section :collapsible="true" :collapsed="true">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="h-5 w-5 text-gray-400" />
                <span>Private Notes for Staff</span>
                @if (!$this->getNotes()->isEmpty())
                    <x-filament::icon icon="heroicon-s-check-circle" class="h-5 w-5 text-success-500" title="Has Notes" />
                @endif
            </div>
        </x-slot>
        <div class="space-y-4">
            {{-- Add Note Button --}}
            <div class="flex justify-end">
                {{ $this->addNoteAction }}
            </div>

            {{-- Notes List --}}
            @if ($this->getNotes()->isEmpty())
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">
                    <svg class="size-12 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">No Notes Yet</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Add the first note to start tracking important information about this application.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($this->getNotes() as $note)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start gap-3">
                                {{-- Profile Image --}}
                                <div class="shrink-0">
                                    @if ($note->createdBy?->profile_picture)
                                        <img
                                            src="{{ Storage::url($note->createdBy->profile_picture) }}"
                                            alt="{{ $note->createdBy->name }}"
                                            class="size-10 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700"
                                        />
                                    @else
                                        <div class="size-10 rounded-full bg-linear-to-br from-primary-500 to-primary-600 flex items-center justify-center ring-2 ring-gray-200 dark:ring-gray-700">
                                            <span class="text-sm font-semibold text-white">
                                                {{ substr($note->createdBy?->name ?? 'U', 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Note Content --}}
                                <div class="flex-1 min-w-0">
                                    {{-- User Info & Timestamp --}}
                                    <div class="flex items-baseline gap-2 mb-2">
                                        <span class="font-semibold text-sm text-gray-900 dark:text-white">
                                            {{ $note->createdBy?->name ?? 'Unknown User' }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $note->created_at->diffForHumans() }}
                                        </span>
                                        @if ($note->createdBy?->is_admin)
                                            <x-filament::badge color="danger" size="xs">
                                                Admin
                                            </x-filament::badge>
                                        @endif
                                    </div>

                                    {{-- Full Timestamp --}}
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        {{ $note->created_at->setTimezone(auth()->user()->timezone)->format('M d, Y \a\t h:i A') }}
                                    </div>

                                    {{-- Note Text --}}
                                    <div class="text-md text-gray-700 dark:text-gray-300 wrap-break-word leading-relaxed">
                                        {{ $note->note }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
