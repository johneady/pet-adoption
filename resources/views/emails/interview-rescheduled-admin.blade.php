<x-mail::message>
# Interview Schedule Updated

Dear Administrator,

This is to inform you that an interview appointment has been rescheduled in the system.

**Interview Details:**
- **Application ID:** #{{ $application->id }}
- **Applicant:** {{ $applicant->name }} ({{ $applicant->email }})
- **Pet:** {{ $pet->name }} ({{ $pet->species->name ?? 'Pet' }})
- **Previous Date & Time:** {{ $oldScheduledAt->timezone($rescheduledBy->timezone ?? App\Models\Setting::get('default_timezone') ?? 'UTC')->format('l, F j, Y \a\t g:i A') }}
- **New Date & Time:** {{ $interview->scheduled_at->timezone(App\Models\Setting::get('default_timezone') ?? 'UTC')->format('l, F j, Y \a\t g:i A') }}
- **Location:** {{ $interview->location ?? 'To be determined' }}
- **Rescheduled By:** {{ $rescheduledBy->name }}

**Action Taken:**
The applicant has been automatically notified of this schedule change via email.

<x-mail::button :url="route('filament.admin.resources.interviews.edit', $interview)">
View Interview Details
</x-mail::button>

Please ensure that all necessary preparations are in place for the updated interview time.

Regards,<br>
{{ App\Models\Setting::get('site_name') }} System
</x-mail::message>
