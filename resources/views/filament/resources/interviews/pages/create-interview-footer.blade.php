<x-filament::section>
    <x-slot name="heading">
        Applicant Contact Information
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Contact Card Column --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col items-center gap-4">
                {{-- Profile Picture --}}
                <div class="relative">
                    @if ($adoptionApplication->user->profile_picture)
                        <img
                            src="{{ Storage::url($adoptionApplication->user->profile_picture) }}"
                            alt="{{ $adoptionApplication->user->name }}"
                            class="size-24 rounded-full object-cover ring-4 ring-gray-100 dark:ring-gray-700"
                        />
                    @else
                        <div class="size-24 rounded-full bg-linear-to-br from-primary-500 to-primary-600 flex items-center justify-center ring-4 ring-gray-100 dark:ring-gray-700">
                            <span class="text-3xl font-semibold text-white">
                                {{ substr($adoptionApplication->user->name, 0, 1) }}
                            </span>
                        </div>
                    @endif

                    {{-- Verification Badge Overlay --}}
                    @if ($adoptionApplication->user->email_verified_at)
                        <div class="absolute bottom-0 right-0 bg-success-500 rounded-full p-1">
                            <svg class="size-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Name & Email Status --}}
                <div class="text-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $adoptionApplication->user->name }}
                    </h3>
                    <div class="flex items-center justify-center gap-2 mt-1">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $adoptionApplication->user->email }}
                        </p>
                        @if ($adoptionApplication->user->email_verified_at)
                            <x-filament::badge color="success" size="xs">
                                Verified
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="gray" size="xs">
                                Not Verified
                            </x-filament::badge>
                        @endif
                    </div>
                </div>

                {{-- Contact Details --}}
                <div class="w-full space-y-4 mt-2">
                    {{-- Phone --}}
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                            <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Phone</div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">
                                {{ $adoptionApplication->user->phone ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                            <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Address</div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">
                                {{ $adoptionApplication->user->address ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>

                    {{-- Member Since --}}
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 size-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                            <svg class="size-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Member Since</div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">
                                {{ $adoptionApplication->user->created_at->format('F j, Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Google Map Column --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if ($adoptionApplication->user->address)
                <iframe
                    width="100%"
                    height="100%"
                    style="min-height: 400px; border: 0;"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://maps.google.com/maps?q={{ urlencode($adoptionApplication->user->address) }}&t=&z=15&ie=UTF8&iwloc=&output=embed"
                ></iframe>
            @else
                <div class="flex items-center justify-center h-full min-h-[400px] bg-gray-50 dark:bg-gray-900/50">
                    <div class="text-center p-6">
                        <svg class="size-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">No Address Available</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The applicant has not provided an address yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament::section>
