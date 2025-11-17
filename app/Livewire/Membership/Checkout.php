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
    }

    public function getPlanProperty()
    {
        return MembershipPlan::where('slug', $this->planSlug)->firstOrFail();
    }

    public function getAmountProperty()
    {
        return $this->type === 'annual' ? $this->plan->annual_price : $this->plan->monthly_price;
    }

    public function render()
    {
        return view('livewire.membership.checkout');
    }
}
