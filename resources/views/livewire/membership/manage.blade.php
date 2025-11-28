<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <!-- Header -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-cover bg-center p-8"
            style="background-image: url('{{ asset('images/default_membership_manage.jpg') }}');">
            <div class="absolute inset-0 bg-zinc-900/45"></div>
            <div class="relative mx-auto max-w-4xl text-center">
                <flux:heading size="xl" class="mb-2 text-white">Manage Membership</flux:heading>
                <flux:text class="text-lg text-white/90">
                    View and manage your membership status and history
                </flux:text>
            </div>
        </div>

        @if ($this->membership && $this->membership->isActive())
            <div class="mb-8 rounded-xl border-2 border-ocean-200 bg-linear-to-br from-ocean-50 via-teal-50 to-ocean-100 p-8 dark:border-ocean-700 dark:from-ocean-950 dark:via-teal-950 dark:to-ocean-900">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg p-3"
                            style="background-color: {{ $this->membership->plan->badge_color }}20">
                            <flux:icon.star class="size-8" style="color: {{ $this->membership->plan->badge_color }}" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-ocean-900 dark:text-ocean-100">{{ $this->membership->plan->name }} Member</flux:heading>
                            <flux:text class="text-ocean-700 dark:text-ocean-300">Active until
                                {{ $this->membership->expires_at->format('M d, Y') }}</flux:text>
                        </div>
                    </div>
                    <flux:badge color="success">Active</flux:badge>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:text class="text-sm font-medium mb-1 text-ocean-700 dark:text-ocean-300">Amount Paid
                        </flux:text>
                        <flux:text class="text-lg text-ocean-900 dark:text-ocean-100">${{ number_format($this->membership->amount_paid, 2) }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium mb-1 text-ocean-700 dark:text-ocean-300">Days Remaining
                        </flux:text>
                        <flux:text class="text-lg text-ocean-900 dark:text-ocean-100">{{ $this->membership->daysRemaining() }} days</flux:text>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-8 rounded-xl border-2 border-ocean-200 bg-linear-to-br from-ocean-50 via-teal-50 to-ocean-100 p-8 dark:border-ocean-700 dark:from-ocean-950 dark:via-teal-950 dark:to-ocean-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-linear-to-br from-ocean-500 to-teal-500 p-3">
                            <flux:icon.heart class="size-8 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="mb-1 text-ocean-900 dark:text-ocean-100">Support Our Mission</flux:heading>
                            <flux:text class="text-ocean-700 dark:text-ocean-300">You don't have an active membership. Consider supporting our mission!</flux:text>
                        </div>
                    </div>
                    <flux:button href="{{ route('membership.plans') }}" variant="primary">
                        View Membership Plans
                    </flux:button>
                </div>
            </div>
        @endif

        <flux:heading size="lg" class="mt-8 mb-4 text-ocean-900 dark:text-ocean-100">Membership History
        </flux:heading>
        <div
            class="overflow-hidden rounded-xl border-2 border-ocean-200 bg-white shadow-sm shadow-ocean-100 dark:border-ocean-800 dark:bg-zinc-900 dark:shadow-ocean-950">
            <table class="w-full">
                <thead class="bg-ocean-50 dark:bg-ocean-950">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase text-ocean-700 dark:text-ocean-300">
                            Plan</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase text-ocean-700 dark:text-ocean-300">
                            Amount</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase text-ocean-700 dark:text-ocean-300">
                            Status</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase text-ocean-700 dark:text-ocean-300">
                            Started</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase text-ocean-700 dark:text-ocean-300">
                            Expires</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ocean-200 dark:divide-ocean-800">
                    @forelse ($this->memberships as $membership)
                        <tr>
                            <td class="px-6 py-4">{{ $membership->plan->name }}</td>
                            <td class="px-6 py-4">${{ number_format($membership->amount_paid, 2) }}</td>
                            <td class="px-6 py-4">
                                <flux:badge
                                    :color="match($membership->status) {
                                                                        'active' => 'success',
                                                                        'expired' => 'danger',
                                                                        'canceled' => 'warning',
                                                                        'refunded' => 'gray',
                                                                        default => 'info'
                                                                    }">
                                    {{ ucfirst($membership->status) }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4">{{ $membership->started_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">{{ $membership->expires_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <flux:text class="text-ocean-700 dark:text-ocean-300">No membership history found.
                                </flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
