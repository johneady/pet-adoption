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

        // Automatically redirect to Stripe checkout
        $this->checkout();
    }

    public function getPlanProperty()
    {
        return MembershipPlan::where('slug', $this->planSlug)->firstOrFail();
    }

    public function checkout()
    {
        $plan = $this->plan;
        $user = auth()->user();

        // If no Stripe Price ID is configured, use the amount directly
        $checkoutOptions = [
            'success_url' => route('membership.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('membership.cancel'),
            'mode' => 'payment',
            'customer_email' => $user->email,
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
        ];

        if ($plan->stripe_price_id) {
            // Use Stripe Price ID (recommended)
            $checkoutOptions['line_items'] = [[
                'price' => $plan->stripe_price_id,
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
                    'unit_amount' => (int) ($plan->price * 100), // Convert to cents
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
