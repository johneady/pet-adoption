<x-mail::message>
# Congratulations! You're a Winner!

We're thrilled to announce that you have won the **{{ $draw->name }}**!

**Winning Details:**
- **Winning Ticket Number:** #{{ $winningTicket->ticket_number }}
- **Prize Amount:** ${{ number_format($prizeAmount, 2) }}

We will be in touch shortly to arrange the transfer of your prize.

Thank you for participating and supporting our cause!

Thanks,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
