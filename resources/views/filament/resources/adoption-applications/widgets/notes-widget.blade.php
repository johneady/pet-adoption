<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Notes
        </x-slot>

        <div class="fi-section-content-ctn grid gap-y-6">
            {{-- Add Note Button --}}
            <div>
                {{ ($this->addNoteAction) }}
            </div>

            {{-- Notes List --}}
            <div class="grid gap-y-3">
                @forelse($this->getNotes() as $note)
                    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <div class="fi-section-content-ctn grid gap-4 p-4 sm:grid-cols-12">
                            {{-- User Info Column --}}
                            <div class="flex items-start gap-3 sm:col-span-3">
                                <x-filament::avatar
                                    size="md"
                                    :alt="$note->createdBy?->name ?? 'Unknown User'"
                                >
                                    <div class="flex items-center justify-center w-full h-full text-sm font-semibold">
                                        {{ $note->createdBy?->initials() ?? '?' }}
                                    </div>
                                </x-filament::avatar>

                                <div class="min-w-0 flex-1">
                                    <div class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                        {{ $note->createdBy?->name ?? 'Unknown User' }}
                                    </div>
                                    <div class="fi-section-header-description text-xs text-gray-500 dark:text-gray-400">
                                        {{ $note->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            {{-- Note Content Column --}}
                            <div class="whitespace-pre-wrap break-words text-sm leading-6 text-gray-950 dark:text-white sm:col-span-9">
                                {{ $note->note }}
                            </div>
                        </div>
                    </div>
                @empty
                    <x-filament::section class="text-center">
                        <div class="fi-section-content-ctn p-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                No notes yet. Add the first note above.
                            </p>
                        </div>
                    </x-filament::section>
                @endforelse
            </div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
