<?php

namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Console\Command;

class ExpireMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memberships:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire memberships that have passed their expiration date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredCount = 0;

        // Find all active memberships that have expired
        $expiredMemberships = Membership::query()
            ->where('status', 'active')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredMemberships as $membership) {
            $membership->update(['status' => 'expired']);

            // Remove current_membership_id from user if this was their current membership
            $user = $membership->user;
            if ($user->current_membership_id === $membership->id) {
                $user->update(['current_membership_id' => null]);
            }

            $expiredCount++;
        }

        $this->info("Expired {$expiredCount} membership(s).");

        return Command::SUCCESS;
    }
}
