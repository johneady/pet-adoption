<div class="py-12">
    <div class="mx-auto max-w-3xl px-6 lg:px-8">
        <flux:heading size="xl" class="mb-8">Checkout - {{ $this->plan->name }} Membership</flux:heading>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-8 mb-8">
            <flux:heading size="lg" class="mb-4">Order Summary</flux:heading>
            <div class="flex justify-between mb-4">
                <flux:text>{{ $this->plan->name }} - Annual</flux:text>
                <flux:text class="font-bold">${{ number_format($this->plan->price, 2) }}</flux:text>
            </div>
            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
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
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.72a.774.774 0 0 1 .763-.648h6.817c2.321 0 4.025.577 5.066 1.716.999 1.092 1.3 2.65.894 4.638l-.003.015c-.036.18-.078.362-.128.546-.483 2.09-1.52 3.757-3.013 4.841-1.441 1.045-3.361 1.574-5.705 1.574H7.652a.641.641 0 0 0-.633.54l-.943 4.295zm5.4-14.283H9.86a.518.518 0 0 0-.511.438l-.753 3.473c-.051.237.122.453.364.453h1.293c.917 0 1.623-.167 2.1-.497.476-.33.764-.853.856-1.557.054-.412.031-.766-.07-1.054-.1-.288-.278-.51-.528-.661-.38-.23-.926-.347-1.635-.595z"/>
                </svg>
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
