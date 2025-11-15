<x-mail::message>
# New User Registration

A new user has registered on {{ config('app.name') }}.

**User Details:**
- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Registered:** {{ $user->created_at->format('F j, Y \a\t g:i A') }}

<x-mail::button :url="$adminUrl">
View User in Admin Panel
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
