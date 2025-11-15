<x-mail::message>
# New Adoption Application

A new adoption application has been submitted on {{ config('app.name') }}.

**Application Details:**
- **Pet:** {{ $pet->name }} ({{ $pet->species->name ?? 'Pet' }})
- **Applicant:** {{ $applicant->name }}
- **Email:** {{ $applicant->email }}
- **Submitted:** {{ $application->created_at->format('F j, Y \a\t g:i A') }}
- **Status:** {{ ucfirst($application->status) }}

<x-mail::button :url="$adminUrl">
View Application in Admin Panel
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
