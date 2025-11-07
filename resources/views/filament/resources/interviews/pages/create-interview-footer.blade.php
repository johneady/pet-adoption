<x-filament::section>
    <x-slot name="heading">
        Applicant Contact Information
    </x-slot>

    <x-slot name="description">
        Contact details for {{ $adoptionApplication->user->name }}
    </x-slot>

    <dl class="grid grid-cols-2 gap-6">
        <div>
            <dt class="fi-section-header-description text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
            <dd class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $adoptionApplication->user->name }}</dd>
        </div>

        <div>
            <dt class="fi-section-header-description text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
            <dd class="mt-1 text-sm font-medium text-gray-950 dark:text-white">
                <a href="mailto:{{ $adoptionApplication->user->email }}" class="text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 underline decoration-primary-600/50">
                    {{ $adoptionApplication->user->email }}
                </a>
            </dd>
        </div>

        <div>
            <dt class="fi-section-header-description text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</dt>
            <dd class="mt-1">
                @if($adoptionApplication->user->email_verified_at)
                    <span class="fi-badge inline-flex items-center gap-1 rounded-md bg-success-50 px-2 py-1 text-xs font-medium text-success-700 ring-1 ring-inset ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Verified
                    </span>
                @else
                    <span class="fi-badge inline-flex items-center gap-1 rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                        Not Verified
                    </span>
                @endif
            </dd>
        </div>

        <div>
            <dt class="fi-section-header-description text-sm font-medium text-gray-500 dark:text-gray-400">Pet</dt>
            <dd class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $adoptionApplication->pet->name }}</dd>
        </div>

        <div>
            <dt class="fi-section-header-description text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
            <dd class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $adoptionApplication->user->created_at->format('F j, Y') }}</dd>
        </div>
    </dl>
</x-filament::section>
