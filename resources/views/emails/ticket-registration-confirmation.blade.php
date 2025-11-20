<x-mail::message>
# Ticket Registration Confirmation

Your draw tickets have been successfully registered!

**Draw Details:**
- **Draw Name:** {{ $draw->name }}
- **Draw End Date:** {{ $draw->ends_at->format('M j, Y g:i A') }}

**Your Tickets:**
@foreach($tickets as $ticket)
- Ticket #{{ $ticket->ticket_number }}
@endforeach

**Purchase Summary:**
- **Number of Tickets:** {{ $tickets->count() }}
- **Total Amount Paid:** ${{ number_format($totalAmount, 2) }}

**Current Draw Status:**
- **Current Prize Pool:** ${{ number_format($prizeAmount, 2) }}
- **Total Tickets Sold:** {{ $totalTicketsSold }}

<x-mail::button :url="route('draws.index')">
View Draw Details
</x-mail::button>

Good luck, and thank you for supporting our cause!

Thanks,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
