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

                <div>
                    <flux:heading size="lg" class="mb-4 text-ocean-900 dark:text-ocean-100">About You & Your Home</flux:heading>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>
                                Living Situation
                                <span class="text-red-600 dark:text-red-400">*</span>
                            </flux:label>
                            <flux:input
                                wire:model="living_situation"
                                placeholder="e.g., House with fenced yard, Apartment with pet policy"
                            />
                            <flux:text size="sm" class="text-ocean-600 dark:text-ocean-400">
                                Describe your home type, yard, and whether you own or rent
                            </flux:text>
                            @error('living_situation')
                                <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label>Household Members</flux:label>
                            <flux:textarea
                                wire:model="household_members"
                                rows="3"
                                placeholder="e.g., 2 adults, 2 children (ages 8 and 12)"
                            />
                            <flux:text size="sm" class="text-ocean-600 dark:text-ocean-400">
                                Who else lives in your home? Include ages of children if applicable
                            </flux:text>
                            @error('household_members')
                                <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label>Employment Status</flux:label>
                            <flux:input
                                wire:model="employment_status"
                                placeholder="e.g., Full-time employed, Work from home"
                            />
                            <flux:text size="sm" class="text-ocean-600 dark:text-ocean-400">
                                This helps us understand who will be home to care for the pet
                            </flux:text>
                            @error('employment_status')
                                <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <flux:separator />

                <div>
                    <flux:heading size="lg" class="mb-4 text-ocean-900 dark:text-ocean-100">Pet Experience</flux:heading>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Previous Pet Experience</flux:label>
                            <flux:textarea
                                wire:model="experience"
                                rows="4"
                                placeholder="Tell us about your experience with pets..."
                            />
                            <flux:text size="sm" class="text-ocean-600 dark:text-ocean-400">
                                Have you had pets before? What types? How long did you care for them?
                            </flux:text>
                            @error('experience')
                                <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label>Current Pets</flux:label>
                            <flux:textarea
                                wire:model="other_pets"
                                rows="3"
                                placeholder="e.g., 1 dog (Golden Retriever, 5 years old, spayed)"
                            />
                            <flux:text size="sm" class="text-ocean-600 dark:text-ocean-400">
                                List any pets you currently have, including species, breed, age, and spay/neuter status
                            </flux:text>
                            @error('other_pets')
                                <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label>Veterinary Reference</flux:label>
                            <flux:input
                                wire:model="veterinary_reference"
                                placeholder="e.g., Dr. Smith at Happy Paws Clinic, (555) 123-4567"
                            />
                            <flux:text size="sm" class="text-ocean-600 dark:text-ocean-400">
                                Name and contact information of your current or previous veterinarian (if applicable)
                            </flux:text>
                            @error('veterinary_reference')
                                <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <flux:separator />

                <div>
                    <flux:heading size="lg" class="mb-4 text-ocean-900 dark:text-ocean-100">Your Adoption Goals</flux:heading>

                    <flux:field>
                        <flux:label>
                            Why do you want to adopt this pet?
                            <span class="text-red-600 dark:text-red-400">*</span>
                        </flux:label>
                        <flux:textarea
                            wire:model="reason_for_adoption"
                            rows="5"
                            placeholder="Share your reasons for wanting to adopt and what you hope to provide for this pet..."
                        />
                        <flux:text size="sm" class="text-ocean-600 dark:text-ocean-400">
                            Tell us what drew you to this pet and how they'll fit into your life
                        </flux:text>
                        @error('reason_for_adoption')
                            <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </flux:field>
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
