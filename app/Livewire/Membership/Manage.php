<?php

namespace App\Livewire\Membership;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Manage extends Component
{
    public function getMembershipProperty()
    {
        return Auth::user()->currentMembership()->with(['plan', 'transactions'])->first();
    }

    public function getMembershipsProperty()
    {
        return Auth::user()->memberships()->with('plan')->latest()->get();
    }

    public function render()
    {
        return view('livewire.membership.manage');
    }
}
