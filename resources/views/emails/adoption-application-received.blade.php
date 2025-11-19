<x-mail::message>
# Application Received!

Dear {{ $user->name }},

Thank you for submitting your adoption application for **{{ $pet->name }}**! We have received your application and our team will review it shortly.

**Application Details:**
- **Pet:** {{ $pet->name }} ({{ $pet->species->name ?? 'Pet' }})
- **Submitted:** {{ $application->created_at->timezone(App\Models\Setting::get('default_timezone'))->format('F j, Y \a\t g:i A') }}
- **Status:** {{ ucfirst($application->status) }}

**Next Steps:**
1. Our team will review your application within 3-5 business days
2. We may contact you for additional information or to schedule a meet-and-greet
3. You'll receive updates via email as your application progresses

**What to Expect:**
- We carefully review all applications to ensure the best match for both you and the pet
- The adoption process may include a home visit and reference checks
- We appreciate your patience as we work to find the perfect home for {{ $pet->name }}

<x-mail::button :url="route('dashboard')">
View Your Dashboard
</x-mail::button>

If you have any questions, please don't hesitate to contact us.

Thanks,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
