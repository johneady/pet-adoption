<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    public bool $receive_new_user_alerts = false;

    public bool $receive_new_adoption_alerts = false;

    public bool $receive_draw_result_alerts = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->receive_new_user_alerts = $user->receive_new_user_alerts;
        $this->receive_new_adoption_alerts = $user->receive_new_adoption_alerts;
        $this->receive_draw_result_alerts = $user->receive_draw_result_alerts;
    }

    /**
     * Update the notification preferences for the currently authenticated user.
     */
    public function updateNotificationPreferences(): void
    {
        $user = Auth::user();

        $user->update([
            'receive_new_user_alerts' => $this->receive_new_user_alerts,
            'receive_new_adoption_alerts' => $this->receive_new_adoption_alerts,
            'receive_draw_result_alerts' => $this->receive_draw_result_alerts,
        ]);

        $this->dispatch('notifications-updated');
    }

    public function render()
    {
        return view('livewire.settings.notifications');
    }
}
