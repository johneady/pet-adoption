<?php

namespace App\Livewire\Membership;

use App\Models\MembershipPlan;
use Livewire\Component;

class Checkout extends Component
{
    public string $planSlug;

    public string $type;

    public function mount(string $plan, string $type = 'annual')
    {
        $this->planSlug = $plan;
        $this->type = $type;

        if (! in_array($this->type, ['annual', 'monthly'])) {
            abort(404);
        }

        // Automatically redirect to Stripe checkout
        $this->checkout();
    }

    public function getPlanProperty()
    {
        return MembershipPlan::where('slug', $this->planSlug)->firstOrFail();
    }

    public function getAmountProperty()
    {
        return $this->type === 'annual' ? $this->plan->annual_price : $this->plan->monthly_price;
    }

    public function checkout()
    {
        $plan = $this->plan;
        $user = auth()->user();

        // Determine which Stripe Price ID to use
        $stripePriceId = $this->type === 'annual'
            ? $plan->stripe_annual_price_id
            : $plan->stripe_monthly_price_id;

        // If no Stripe Price ID is configured, use the amount directly
        $checkoutOptions = [
            'success_url' => route('membership.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('membership.cancel'),
            'mode' => 'payment',
            'customer_email' => $user->email,
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'payment_type' => $this->type,
            ],
        ];

        if ($stripePriceId) {
            // Use Stripe Price ID (recommended)
            $checkoutOptions['line_items'] = [[
                'price' => $stripePriceId,
                'quantity' => 1,
            ]];
        } else {
            // Use amount directly (fallback)
            $checkoutOptions['line_items'] = [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $plan->name.' Membership',
                        'description' => $plan->description ?? 'Support our mission with a '.$plan->name.' membership',
                    ],
                    'unit_amount' => (int) ($this->amount * 100), // Convert to cents
                ],
                'quantity' => 1,
            ]];
        }

        $checkout = $user->checkout($checkoutOptions);

        return redirect($checkout->url);
    }

    public function render()
    {
        return view('livewire.membership.checkout');
    }
}
