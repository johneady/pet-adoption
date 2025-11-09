<x-filament-widgets::widget>
    <x-filament::section collapsible>
        <x-slot name="heading">
            Notes
        </x-slot>

        <div class="fi-wi-stats-overview-stat-card grid gap-y-6">
            {{-- Add Note Button --}}
            <div>
                {{ $this->addNoteAction }}
            </div>
            {{-- Notes List --}}
            @if ($this->getNotes()->isEmpty())
                <br>
                <x-filament::section>
                    <div class="fi-section-content-ctn text-center">
                        <p class="fi-section-header-description">
                            No notes yet. Add the first note above.
                        </p>
                    </div>
                </x-filament::section>
            @else
                <div class="grid grid-cols-2 gap-4">
                    @foreach ($this->getNotes() as $note)
                        <br>
                        <x-filament::section>
                            <div class="flex flex-col gap-3">
                                {{-- User Info --}}
                                <div class="flex items-start gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="fi-section-header-heading text-base">
                                            {{ $note->createdBy?->name ?? 'Unknown User' }}
                                        </div>
                                        <div class="fi-section-header-description">
                                            {{ $note->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Note Content --}}
                                <div class="whitespace-pre-wrap break-words">
                                    {{ $note->note }}
                                </div>
                            </div>
                        </x-filament::section>
                    @endforeach
                </div>
            @endif
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
