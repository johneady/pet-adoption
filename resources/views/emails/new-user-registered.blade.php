<x-mail::message>
# New User Registration

A new user has registered on {{ App\Models\Setting::get('site_name') }}.

**User Details:**
- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Registered:** {{ $user->created_at->timezone(App\Models\Setting::get('default_timezone'))->format('F j, Y \a\t g:i A') }}

<x-mail::button :url="$adminUrl">
View User in Admin Panel
</x-mail::button>

Thanks,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
