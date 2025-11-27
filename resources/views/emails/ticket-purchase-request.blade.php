<x-mail::message>
# New Ticket Purchase Request

A user has submitted a request to purchase draw tickets.

**Request Details:**
- **User:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Draw:** {{ $draw->name }}
- **Quantity:** {{ $request->quantity }} ticket{{ $request->quantity > 1 ? 's' : '' }}
- **Pricing Tier:** ${{ number_format($pricingTier['price'], 2) }}
- **Requested At:** {{ $request->created_at->timezone(App\Models\Setting::get('default_timezone'))->format('M j, Y g:i A') }}

<x-mail::button :url="config('app.url') . '/admin/ticket-purchase-requests'">
View Draw in Admin Panel
</x-mail::button>

Please log in to the admin panel to register these tickets for the user.

Thanks,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
