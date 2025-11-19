<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">
        <!-- Header -->
        <div class="mb-8 rounded-2xl bg-gradient-to-br from-ocean-50 to-teal-50 p-8 dark:from-ocean-950 dark:to-teal-950">
            <flux:heading size="xl" class="mb-2 text-ocean-900 dark:text-ocean-100">Adoption Application</flux:heading>
            <flux:text class="text-ocean-700 dark:text-ocean-300">
                Please complete this application form to start your adoption journey. We'll review your application and contact you within 2-3 business days.
            </flux:text>
        </div>

        <form wire:submit="submit">
            <div class="space-y-6 rounded-xl border-2 border-ocean-200 bg-white p-6 shadow-sm shadow-ocean-100 dark:border-ocean-800 dark:bg-zinc-900 dark:shadow-ocean-950">
                <div>
                    <flux:heading size="lg" class="mb-4 text-ocean-900 dark:text-ocean-100">Pet Selection</flux:heading>

                    @if($selectedPet)
                        <div class="rounded-xl border-2 border-ocean-300 bg-gradient-to-br from-white to-ocean-50/50 p-6 shadow-lg shadow-ocean-100 dark:border-ocean-700 dark:from-zinc-900 dark:to-ocean-950/30 dark:shadow-ocean-950">
                            <flux:heading size="md" class="mb-4 text-ocean-900 dark:text-ocean-100">Applying to Adopt</flux:heading>

                            <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                                <!-- Pet Photo -->
                                <div class="flex-shrink-0">
                                    <div class="relative h-48 w-48 overflow-hidden rounded-lg border-2 border-ocean-300 bg-gradient-to-br from-ocean-50 to-teal-50 shadow-md dark:border-ocean-700 dark:from-ocean-950 dark:to-zinc-800">
                                        @if($selectedPet->photos->isNotEmpty())
                                            @php
                                                $primaryPhoto = $selectedPet->photos->firstWhere('is_primary', true) ?? $selectedPet->photos->first();
                                            @endphp
                                            <img src="{{ Storage::url($primaryPhoto->file_path) }}"
                                                 alt="{{ $selectedPet->name }}"
                                                 class="h-full w-full object-cover"
                                                 onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                                <svg class="h-16 w-16 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="flex h-full w-full items-center justify-center">
                                                <svg class="h-16 w-16 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="absolute left-2 top-2">
                                            <flux:badge variant="success" size="sm" class="border-ocean-600 bg-ocean-500 backdrop-blur-sm">Available</flux:badge>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pet Details -->
                                <div class="flex-1">
                                    <div class="mb-3">
                                        <flux:heading size="lg" class="mb-1 text-ocean-900 dark:text-ocean-100">{{ $selectedPet->name }}</flux:heading>
                                        <flux:text class="text-ocean-700 dark:text-ocean-300">
                                            {{ $selectedPet->breed?->name ?? $selectedPet->species->name }}
                                        </flux:text>
                                    </div>

                                    <!-- Quick Info Grid -->
                                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                        @if($selectedPet->age)
                                            <div class="rounded-lg border border-ocean-200 bg-white/50 px-3 py-2 dark:border-ocean-800 dark:bg-zinc-800/50">
                                                <flux:text size="xs" class="text-ocean-600 dark:text-ocean-400">Age</flux:text>
                                                <flux:text size="sm" class="font-semibold text-ocean-900 dark:text-ocean-100">{{ $selectedPet->age }} {{ Str::plural('year', $selectedPet->age) }}</flux:text>
                                            </div>
                                        @endif

                                        @if($selectedPet->gender)
                                            <div class="rounded-lg border border-ocean-200 bg-white/50 px-3 py-2 dark:border-ocean-800 dark:bg-zinc-800/50">
                                                <flux:text size="xs" class="text-ocean-600 dark:text-ocean-400">Gender</flux:text>
                                                <flux:text size="sm" class="font-semibold text-ocean-900 dark:text-ocean-100">{{ ucfirst($selectedPet->gender) }}</flux:text>
                                            </div>
                                        @endif

                                        @if($selectedPet->size)
                                            <div class="rounded-lg border border-ocean-200 bg-white/50 px-3 py-2 dark:border-ocean-800 dark:bg-zinc-800/50">
                                                <flux:text size="xs" class="text-ocean-600 dark:text-ocean-400">Size</flux:text>
                                                <flux:text size="sm" class="font-semibold text-ocean-900 dark:text-ocean-100">{{ ucfirst(str_replace('_', ' ', $selectedPet->size)) }}</flux:text>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-4 rounded-lg border border-ocean-200 bg-ocean-50/50 p-3 dark:border-ocean-800 dark:bg-ocean-950/20">
                                        <flux:text size="sm" class="text-ocean-800 dark:text-ocean-200">
                                            This application is for {{ $selectedPet->name }}. To apply for a different pet, please return to the pet listings.
                                        </flux:text>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <flux:field>
                            <flux:label>
                                Which pet would you like to adopt?
                                <span class="text-red-600 dark:text-red-400">*</span>
                            </flux:label>
                            <flux:select wire:model="pet_id">
                                <option value="">Select a pet</option>
                                @foreach($availablePets as $pet)
                                    <option value="{{ $pet->id }}">
                                        {{ $pet->name }} - {{ $pet->species->name }}@if($pet->breed), {{ $pet->breed->name }}@endif
                                    </option>
                                @endforeach
                            </flux:select>
                            @error('pet_id')
                                <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </flux:field>
                    @endif
                </div>

                <flux:separator />

                <!-- Dynamic Questions -->
                <div>
                    <flux:heading size="lg" class="mb-4 text-ocean-900 dark:text-ocean-100">Application Questions</flux:heading>

                    <div class="space-y-4">
                        @foreach($questions as $question)
                            <flux:field>
                                <flux:label>
                                    {{ $question->label }}
                                    @if($question->is_required)
                                        <span class="text-red-600 dark:text-red-400">*</span>
                                    @endif
                                </flux:label>

                                @switch($question->type->value)
                                    @case('string')
                                        <flux:input
                                            wire:model="answers.{{ $question->id }}"
                                        />
                                        @break

                                    @case('textarea')
                                        <flux:textarea
                                            wire:model="answers.{{ $question->id }}"
                                            rows="4"
                                        />
                                        @break

                                    @case('dropdown')
                                        <flux:select wire:model="answers.{{ $question->id }}">
                                            <option value="">Select an option</option>
                                            @foreach($question->options ?? [] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </flux:select>
                                        @break

                                    @case('switch')
                                        <div class="pt-1">
                                            <flux:switch
                                                wire:model="answers.{{ $question->id }}"
                                                label="Yes"
                                            />
                                        </div>
                                        @break
                                @endswitch

                                @error("answers.{$question->id}")
                                    <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                                @enderror
                            </flux:field>
                        @endforeach
                    </div>
                </div>

                <flux:separator />

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submit">Submit Application</span>
                        <span wire:loading wire:target="submit">Submitting...</span>
                    </flux:button>
                    <flux:button
                        type="button"
                        variant="ghost"
                        wire:click="$dispatch('navigate', { url: '{{ route('dashboard') }}' })"
                    >
                        Cancel
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</div>
