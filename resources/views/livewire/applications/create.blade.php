<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">
        <div class="mb-8">
            <flux:heading size="xl" class="mb-2">Adoption Application</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Please complete this application form to start your adoption journey. We'll review your application and contact you within 2-3 business days.
            </flux:text>
        </div>

        <form wire:submit="submit">
            <div class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div>
                    <flux:heading size="lg" class="mb-4">Pet Selection</flux:heading>

                    @if($selectedPet)
                        <div class="rounded-lg border-2 border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/30">
                            <flux:field>
                                <flux:label>
                                    Selected Pet
                                </flux:label>
                                <div class="rounded-lg border border-blue-300 bg-white p-4 dark:border-blue-700 dark:bg-zinc-900">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <div>
                                            <flux:text class="font-semibold">{{ $selectedPet->name }}</flux:text>
                                            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                                                {{ $selectedPet->species->name }}@if($selectedPet->breed), {{ $selectedPet->breed->name }}@endif
                                            </flux:text>
                                        </div>
                                    </div>
                                </div>
                                <flux:text size="sm" class="mt-2 text-blue-700 dark:text-blue-300">
                                    This application is for {{ $selectedPet->name }}. To apply for a different pet, please return to the pet listings.
                                </flux:text>
                            </flux:field>
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
                    <flux:heading size="lg" class="mb-4">About You & Your Home</flux:heading>

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
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
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
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
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
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
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
                    <flux:heading size="lg" class="mb-4">Pet Experience</flux:heading>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Previous Pet Experience</flux:label>
                            <flux:textarea
                                wire:model="experience"
                                rows="4"
                                placeholder="Tell us about your experience with pets..."
                            />
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
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
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
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
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
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
                    <flux:heading size="lg" class="mb-4">Your Adoption Goals</flux:heading>

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
                        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
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
