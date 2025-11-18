<?php

namespace App\Livewire\Membership;

use App\Models\MembershipPlan;
use Livewire\Component;

class Checkout extends Component
{
    public string $planSlug;

    public function mount(string $plan)
    {
        $this->planSlug = $plan;
    }

    public function getPlanProperty()
    {
        return MembershipPlan::where('slug', $this->planSlug)->firstOrFail();
    }

    /**
     * Get PayPal form action URL.
     */
    public function getPaypalUrlProperty(): string
    {
        return config('services.paypal.mode') === 'sandbox'
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';
    }

    /**
     * Get PayPal business email.
     */
    public function getPaypalEmailProperty(): string
    {
        return config('services.paypal.email');
    }

    /**
     * Get custom data for PayPal (JSON encoded).
     */
    public function getCustomDataProperty(): string
    {
        return json_encode([
            'user_id' => auth()->id(),
            'plan_id' => $this->plan->id,
        ]);
    }

    /**
     * Get the IPN notify URL.
     */
    public function getNotifyUrlProperty(): string
    {
        return route('webhooks.paypal-ipn');
    }

    public function render()
    {
        return view('livewire.membership.checkout');
    }
}
