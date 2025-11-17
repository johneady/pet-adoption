<div class="py-12">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <flux:heading size="xl" class="mb-8">Manage Membership</flux:heading>

        @if ($this->membership && $this->membership->isActive())
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg p-3" style="background-color: {{ $this->membership->plan->badge_color }}20">
                            <flux:icon.star class="size-8" style="color: {{ $this->membership->plan->badge_color }}" />
                        </div>
                        <div>
                            <flux:heading size="lg">{{ $this->membership->plan->name }} Member</flux:heading>
                            <flux:text>Active until {{ $this->membership->expires_at->format('M d, Y') }}</flux:text>
                        </div>
                    </div>
                    <flux:badge color="success">Active</flux:badge>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <flux:text class="text-sm font-medium mb-1">Payment Type</flux:text>
                        <flux:text class="text-lg">{{ ucfirst($this->membership->payment_type) }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium mb-1">Amount Paid</flux:text>
                        <flux:text class="text-lg">${{ number_format($this->membership->amount_paid, 2) }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium mb-1">Days Remaining</flux:text>
                        <flux:text class="text-lg">{{ $this->membership->daysRemaining() }} days</flux:text>
                    </div>
                </div>
            </div>
        @else
            <flux:callout color="warning" class="mb-8">
                You don't have an active membership. Consider supporting our mission!
            </flux:callout>
            <flux:button href="{{ route('membership.plans') }}" variant="primary">
                View Membership Plans
            </flux:button>
        @endif

        <flux:heading size="lg" class="mt-8 mb-4">Membership History</flux:heading>
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Started</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Expires</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($this->memberships as $membership)
                        <tr>
                            <td class="px-6 py-4">{{ $membership->plan->name }}</td>
                            <td class="px-6 py-4">{{ ucfirst($membership->payment_type) }}</td>
                            <td class="px-6 py-4">${{ number_format($membership->amount_paid, 2) }}</td>
                            <td class="px-6 py-4">
                                <flux:badge :color="match($membership->status) {
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
                            <td colspan="6" class="px-6 py-8 text-center">
                                <flux:text>No membership history found.</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
