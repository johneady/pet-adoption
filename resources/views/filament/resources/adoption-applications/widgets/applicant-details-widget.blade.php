@php
    $applicant = $this->getApplicant();
    $totalApplications = $this->getTotalApplications();
@endphp

<x-filament-widgets::widget>
    <x-filament::section heading="{{ $applicant->name }}" description="Information about {{ $applicant->name }}"
        :collapsible="true" :collapsed="true">
        @if ($applicant)
            <div class="space-y-6">
                <div class="flex flex-col gap-1.5">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $applicant->name }}</dd>
                </div>

                <div class="flex flex-col gap-1.5">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="flex flex-wrap items-center gap-2">
                        <span class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $applicant->email }}</span>
                        @if ($applicant->email_verified_at)
                            <x-filament::badge color="success" icon="heroicon-m-check-circle">
                                Email Verified
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="warning" icon="heroicon-m-exclamation-triangle">
                                Email Not Verified
                            </x-filament::badge>
                        @endif
                        @if ($applicant->is_admin)
                            <x-filament::badge color="danger" icon="heroicon-m-shield-check">
                                Admin
                            </x-filament::badge>
                        @endif
                    </dd>
                </div>

                <div class="flex flex-col gap-1.5">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                    <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $applicant->created_at->format('M d, Y') }}</dd>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">No applicant information available.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
