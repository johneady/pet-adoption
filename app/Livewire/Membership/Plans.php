<?php

namespace App\Livewire\Membership;

use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Plans extends Component
{
    public function getPlansProperty(): Collection
    {
        return MembershipPlan::query()
            ->active()
            ->ordered()
            ->get();
    }

    public function render()
    {
        return view('livewire.membership.plans');
    }
}
