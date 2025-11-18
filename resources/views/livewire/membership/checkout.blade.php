<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">
        <!-- Header -->
        <div class="mb-8 rounded-2xl bg-gradient-to-br from-ocean-50 to-teal-50 p-8 dark:from-ocean-950 dark:to-teal-950">
            <flux:heading size="xl" class="mb-2 text-ocean-900 dark:text-ocean-100">Checkout</flux:heading>
            <flux:text class="text-ocean-700 dark:text-ocean-300">Complete your {{ $this->plan->name }} Membership purchase</flux:text>
        </div>

        <div class="rounded-xl border-2 border-ocean-200 bg-white p-8 shadow-sm shadow-ocean-100 dark:border-ocean-800 dark:bg-zinc-900 dark:shadow-ocean-950 mb-8">
            <flux:heading size="lg" class="mb-4 text-ocean-900 dark:text-ocean-100">Order Summary</flux:heading>
            <div class="flex justify-between mb-4">
                <flux:text>{{ $this->plan->name }} - Annual</flux:text>
                <flux:text class="font-bold">${{ number_format($this->plan->price, 2) }}</flux:text>
            </div>
            <flux:text class="text-sm text-ocean-600 dark:text-ocean-400">
                One-time annual payment
            </flux:text>
        </div>

        <!-- PayPal Checkout Form -->
        <form action="{{ $this->paypalUrl }}" method="POST" class="mb-8">
            <!-- PayPal Standard fields -->
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="{{ $this->paypalEmail }}">
            <input type="hidden" name="item_name" value="{{ $this->plan->name }} Membership">
            <input type="hidden" name="item_number" value="{{ $this->plan->slug }}">
            <input type="hidden" name="amount" value="{{ $this->plan->price }}">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="no_note" value="1">

            <!-- Custom data for IPN -->
            <input type="hidden" name="custom" value="{{ $this->customData }}">

            <!-- URLs -->
            <input type="hidden" name="return" value="{{ route('membership.success') }}">
            <input type="hidden" name="cancel_return" value="{{ route('membership.cancel') }}">
            <input type="hidden" name="notify_url" value="{{ $this->notifyUrl }}">

            <flux:button type="submit" variant="primary" class="w-full">
                Pay with PayPal
            </flux:button>
        </form>

        <div class="text-center">
            <flux:button href="{{ route('membership.plans') }}" variant="ghost">
                Back to Plans
            </flux:button>
        </div>

        <flux:callout color="info" class="mt-8">
            <strong>Secure Payment</strong><br>
            You will be redirected to PayPal to complete your payment securely. After payment, you will be returned to this site and your membership will be activated automatically.
        </flux:callout>
    </div>
</div>
