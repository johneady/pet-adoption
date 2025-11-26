<x-mail::message>
# Interview Scheduled

Dear Administrator,

This is to inform you that an interview appointment has been scheduled in the system.

**Interview Details:**
- **Application ID:** #{{ $application->id }}
- **Applicant:** {{ $applicant->name }} ({{ $applicant->email }})
- **Pet:** {{ $pet->name }} ({{ $pet->species->name ?? 'Pet' }})
- **Date & Time:**
{{ $interview->scheduled_at->timezone($scheduledBy->timezone ?? 'UTC')->format('l, F j, Y \a\t g:i A') }}
-  **Location:** {{ $interview->location ?? 'To be determined' }}
- **Scheduled By:** {{ $scheduledBy->name }}

**Action Taken:**
The applicant has been automatically notified of this interview via email.

<x-mail::button :url="route('filament.admin.resources.interviews.edit', $interview)">
        View Interview Details
</x-mail::button>

Please ensure that all necessary preparations are in place for the interview.

Regards,<br>
{{ App\Models\Setting::get('site_name') }} System
</x-mail::message>
