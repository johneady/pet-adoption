<x-mail::message>
# Welcome to {{ App\Models\Setting::get('site_name') }}, {{ $user->name }}!

Thank you for verifying your email and joining our pet adoption community. We're thrilled to have you here!

Our mission is to connect loving families with pets in need of a forever home. Browse through our available pets and find your perfect companion.

<x-mail::button :url="route('pets.index')">
Browse Available Pets
</x-mail::button>

If you have any questions or need assistance, please don't hesitate to reach out to us.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
