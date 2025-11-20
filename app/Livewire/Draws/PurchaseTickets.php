<?php

namespace App\Livewire\Draws;

use App\Mail\TicketPurchaseRequest as TicketPurchaseRequestMail;
use App\Models\Draw;
use App\Models\TicketPurchaseRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class PurchaseTickets extends Component
{
    public Draw $draw;

    public ?string $selectedPricingTier = null;

    /**
     * Mount the component.
     */
    public function mount(Draw $draw): void
    {
        // Ensure the draw is active
        if (! $draw->isActive()) {
            abort(404, 'This draw is not currently active.');
        }

        $this->draw = $draw;
    }

    /**
     * Submit the purchase request.
     */
    public function submit(): void
    {
        $this->validate([
            'selectedPricingTier' => 'required|string',
        ], [
            'selectedPricingTier.required' => 'Please select a ticket package.',
        ]);

        $pricingTier = json_decode($this->selectedPricingTier, true);

        // Create the purchase request
        $request = TicketPurchaseRequest::create([
            'draw_id' => $this->draw->id,
            'user_id' => Auth::id(),
            'quantity' => $pricingTier['quantity'],
            'pricing_tier' => $pricingTier,
            'status' => 'pending',
        ]);

        // Send email to admins with the notification preference enabled
        $admins = User::where('is_admin', true)
            ->where('receive_ticket_purchase_alerts', true)
            ->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new TicketPurchaseRequestMail($request));
        }

        session()->flash('message', 'Your ticket purchase request has been submitted! An administrator will process your request shortly.');

        $this->redirect(route('draws.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.draws.purchase-tickets');
    }
}
