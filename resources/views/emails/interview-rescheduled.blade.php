<x-mail::message>
# Interview Rescheduled

Dear {{ $applicant->name }},

We wanted to inform you that your interview for the adoption of **{{ $pet->name }}** has been rescheduled to a new date and time.

**Updated Interview Details:**
- **Pet:** {{ $pet->name }} ({{ $pet->species->name ?? 'Pet' }})
- **Previous Date & Time:** {{ $oldScheduledAt->timezone($applicant->timezone)->format('l, F j, Y \a\t g:i A') }}
- **New Date & Time:** {{ $interview->scheduled_at->timezone($applicant->timezone)->format('l, F j, Y \a\t g:i A') }}
- **Location:** {{ $interview->location ?? 'To be determined' }}
- **Updated By:** {{ $rescheduledBy->name }}

**What to Expect:**
- The interview typically lasts 30-60 minutes
- We'll discuss your experience with pets and your home environment
- Feel free to ask any questions you have about {{ $pet->name }}
- Please arrive a few minutes early if meeting in person

**Preparation Tips:**
- Review your application details beforehand
- Prepare any questions about {{ $pet->name }}'s care requirements
- If it's a home visit, ensure your space is ready to be evaluated
- For video calls, test your connection in advance

<x-mail::button :url="route('dashboard')">
View Your Application
</x-mail::button>

We apologize for any inconvenience this change may cause. If you have any questions or concerns about the new date and time, please don't hesitate to contact us.

We look forward to meeting with you!

Thanks,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
