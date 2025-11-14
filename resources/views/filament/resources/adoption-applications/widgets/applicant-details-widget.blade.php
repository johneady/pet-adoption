@php
    $applicant = $this->getApplicant();
    $totalApplications = $this->getTotalApplications();
@endphp

<x-filament-widgets::widget>
    <x-filament::section heading="Applicant Details" description="Information about the person applying to adopt"
        :collapsible="true" :collapsed="false">
        @if ($applicant)
            <table>
                <tr>
                    <td class="text-sm font-semibold text-gray-700 dark:text-gray-300">Name: </td>
                    <td>{{ $applicant->name }}</td>
                </tr>

                <tr>
                    <td class="text-sm font-semibold text-gray-700 dark:text-gray-300">Email:</td>
                    <td>
                        {{ $applicant->email }}
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
                    </td>
                </tr>

                <tr>
                    <td class="text-sm font-semibold text-gray-700 dark:text-gray-300">Member Since:</td>
                    <td>{{ $applicant->created_at->format('M d, Y') }}</td>
                </tr>
            </table>
        @else
            <p>No applicant information available.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
