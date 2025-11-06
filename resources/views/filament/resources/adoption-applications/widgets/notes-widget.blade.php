<x-filament-widgets::widget>
    <x-filament::section collapsible>
        <x-slot name="heading">
            Notes
        </x-slot>

        <div class="fi-wi-stats-overview-stat-card grid gap-y-6">
            {{-- Add Note Button --}}
            <div>
                {{ ($this->addNoteAction) }}
            </div>

            {{-- Notes List --}}
            <div class="fi-wi-stats-overview-stat-card grid gap-y-3">
                @forelse($this->getNotes() as $note)
                    <x-filament::section>
                        <div class="flex gap-4">
                            {{-- User Info Column --}}
                            <div class="flex w-1/4 shrink-0 items-start gap-3">
                                <x-filament::avatar
                                    size="md"
                                    :src="$note->createdBy ? 'https://ui-avatars.com/api/?name=' . urlencode($note->createdBy->name) . '&color=FFFFFF&background=111827' : null"
                                    :alt="$note->createdBy?->name ?? 'Unknown User'"
                                />

                                <div class="min-w-0 flex-1">
                                    <div class="fi-section-header-heading text-base">
                                        {{ $note->createdBy?->name ?? 'Unknown User' }}
                                    </div>
                                    <div class="fi-section-header-description">
                                        {{ $note->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            {{-- Note Content Column --}}
                            <div class="flex-1 whitespace-pre-wrap break-words">
                                {{ $note->note }}
                            </div>
                        </div>
                    </x-filament::section>
                @empty
                    <x-filament::section>
                        <div class="fi-section-content-ctn text-center">
                            <p class="fi-section-header-description">
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
