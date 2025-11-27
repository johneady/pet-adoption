<?php

namespace App\Livewire\Draws;

use App\Models\Draw;
use App\Models\TicketPurchaseRequest;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Index extends Component
{
    /**
     * Get the currently active draw.
     */
    public function getActiveDrawProperty(): ?Draw
    {
        return Draw::query()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->where('is_finalized', false)
            ->withCount('tickets')
            ->withSum('tickets', 'amount_paid')
            ->first();
    }

    /**
     * Get past draws that are finalized.
     */
    public function getPastDrawsProperty(): Collection
    {
        return Draw::query()
            ->where('is_finalized', true)
            ->with(['winnerTicket.user'])
            ->withCount('tickets')
            ->withSum('tickets', 'amount_paid')
            ->orderBy('ends_at', 'desc')
            ->get();
    }

    /**
     * Get upcoming draws.
     */
    public function getUpcomingDrawsProperty(): Collection
    {
        return Draw::query()
            ->where('starts_at', '>', now())
            ->where('is_finalized', false)
            ->orderBy('starts_at', 'asc')
            ->get();
    }

    /**
     * Get the authenticated user's tickets for the active draw.
     */
    public function getUserTicketsProperty(): Collection
    {
        if (! auth()->check() || ! $this->activeDraw) {
            return collect();
        }

        return auth()->user()->drawTickets()
            ->where('draw_id', $this->activeDraw->id)
            ->orderBy('ticket_number')
            ->get();
    }

    /**
     * Get the authenticated user's pending ticket purchase requests for the active draw.
     */
    public function getPendingPurchaseRequestsProperty(): Collection
    {
        if (! auth()->check() || ! $this->activeDraw) {
            return collect();
        }

        return TicketPurchaseRequest::query()
            ->where('user_id', auth()->id())
            ->where('draw_id', $this->activeDraw->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.draws.index');
    }
}
