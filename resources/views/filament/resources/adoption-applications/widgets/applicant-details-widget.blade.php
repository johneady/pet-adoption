@php
    $applicant = $this->getApplicant();
    $totalApplications = $this->getTotalApplications();
@endphp

<x-filament-widgets::widget>
    <x-filament::section
        heading="Applicant Details"
        description="Information about the person applying to adopt"
        :collapsible="true"
        :collapsed="false"
    >
        @if($applicant)
            <table class="px-3 py-3">
                <div>
                    <div>
                        {{ $applicant->initials() }}
                    </div>
                </div>

                <div>
                    <h3>{{ $applicant->name }}</h3>
                    <div>
                        @if($applicant->email_verified_at)
                            <x-filament::badge color="success" icon="heroicon-m-check-circle">
                                Email Verified
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="warning" icon="heroicon-m-exclamation-triangle">
                                Email Not Verified
                            </x-filament::badge>
                        @endif
                        @if($applicant->is_admin)
                            <x-filament::badge color="danger" icon="heroicon-m-shield-check">
                                Admin
                            </x-filament::badge>
                        @endif
                    </div>
                </div>

                <tr>
                    <td class="fi-section-header-heading font-semibold">Email:</td>
                    <td>
                        <a href="mailto:{{ $applicant->email }}" class="flex items-center gap-1">
                            <x-filament::icon icon="heroicon-m-envelope" class="w-4 h-4" />
                            {{ $applicant->email }}
                        </a>
                    </td>
                </tr>

                <tr>
                    <td class="fi-section-header-heading font-semibold">Member Since:</td>
                    <td>{{ $applicant->created_at->format('M d, Y') }}</td>
                </tr>

                <tr>
                    <td class="fi-section-header-heading font-semibold">Total Applications:</td>
                    <td>{{ $totalApplications }}</td>
                </tr>

                @if($applicant->email_verified_at)
                    <tr>
                        <td class="fi-section-header-heading font-semibold">Email Verified:</td>
                        <td>{{ $applicant->email_verified_at->format('M d, Y') }}</td>
                    </tr>
                @endif

                <tr>
                    <td class="fi-section-header-heading font-semibold">Account ID:</td>
                    <td>#{{ $applicant->id }}</td>
                </tr>

                <tr>
                    <td class="fi-section-header-heading font-semibold">Account Status:</td>
                    <td>{{ $applicant->email_verified_at ? 'Active' : 'Pending Verification' }}</td>
                </tr>
            </table>
        @else
            <p>No applicant information available.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
