<?php

namespace App\Livewire\Membership;

use Livewire\Component;

class Success extends Component
{
    public ?string $sessionId = null;

    public function mount()
    {
        $this->sessionId = request()->query('session_id');

        if (! $this->sessionId) {
            return redirect()->route('membership.plans');
        }
    }

    public function render()
    {
        return view('livewire.membership.success');
    }
}
