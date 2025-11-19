<x-mail::message>
# Interview Scheduled!

Dear {{ $applicant->name }},

Great news! We have scheduled an interview for your adoption application for **{{ $pet->name }}**. This is an exciting step forward in the adoption process!

**Interview Details:**
- **Pet:** {{ $pet->name }} ({{ $pet->species->name ?? 'Pet' }})
- **Date & Time:** {{ $interview->scheduled_at->timezone($applicant->timezone)->format('l, F j, Y \a\t g:i A') }}
- **Location:** {{ $interview->location ?? 'To be determined' }}
- **Scheduled By:** {{ $scheduledBy->name }}

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

If you need to reschedule or have any questions, please contact us as soon as possible.

We look forward to meeting with you!

Thanks,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
