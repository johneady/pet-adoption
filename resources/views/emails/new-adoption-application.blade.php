<x-mail::message>
# New Adoption Application

A new adoption application has been submitted on {{ App\Models\Setting::get('site_name') }}.

**Application Details:**
- **Pet:** {{ $pet->name }} ({{ $pet->species->name ?? 'Pet' }})
- **Applicant:** {{ $applicant->name }}
- **Email:** {{ $applicant->email }}
- **Submitted:** {{ $application->created_at->timezone(App\Models\Setting::get('default_timezone'))->format('F j, Y \a\t g:i A') }}
- **Status:** {{ ucfirst($application->status) }}

<x-mail::button :url="$adminUrl">
View Application in Admin Panel
</x-mail::button>

Thanks,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
