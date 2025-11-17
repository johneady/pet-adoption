<div class="py-12">
    <div class="mx-auto max-w-3xl px-6 lg:px-8">
        <flux:heading size="xl" class="mb-8">Checkout - {{ $this->plan->name }} Membership</flux:heading>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-8 mb-8">
            <flux:heading size="lg" class="mb-4">Order Summary</flux:heading>
            <div class="flex justify-between mb-4">
                <flux:text>{{ $this->plan->name }} - {{ ucfirst($this->type) }}</flux:text>
                <flux:text class="font-bold">${{ number_format($this->amount, 2) }}</flux:text>
            </div>
            <flux:text class="text-sm">
                {{ $this->type === 'annual' ? 'One-time annual payment' : '12 monthly payments' }}
            </flux:text>
        </div>

        <flux:callout color="info" class="mb-8">
            <strong>Payment Processing Placeholder</strong><br>
            To complete the checkout integration, you need to:
            <ol class="mt-2 ml-4 list-decimal space-y-1">
                <li>Configure your Stripe API keys in the .env file</li>
                <li>Create Stripe Price IDs for each plan</li>
                <li>Implement the Stripe Checkout session</li>
                <li>Set up webhook handlers for payment events</li>
            </ol>
        </flux:callout>

        <flux:button href="{{ route('membership.plans') }}" variant="outline">
            Back to Plans
        </flux:button>
    </div>
</div>
