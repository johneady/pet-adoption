<x-mail::message>
# 50/50 Draw Results Summary

The **{{ $draw->name }}** has concluded and a winner has been selected.

## Draw Statistics

| Metric | Value |
|:-------|------:|
| Duration | {{ $durationDays }} days |
| Total Tickets Sold | {{ $totalTickets }} |
| Unique Participants | {{ $uniqueParticipants }} |
| Total Amount Collected | ${{ number_format($totalAmount, 2) }} |
| Prize Amount | ${{ number_format($prizeAmount, 2) }} |

## Winner Details

- **Winner:** {{ $winningTicket->user->name }}
- **Email:** {{ $winningTicket->user->email }}
- **Winning Ticket:** #{{ $winningTicket->ticket_number }}

<x-mail::button :url="route('filament.admin.resources.draws.edit', $draw)">
View Draw in Admin Panel
</x-mail::button>

Thanks,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
