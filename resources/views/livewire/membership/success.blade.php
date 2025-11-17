<div class="mx-auto max-w-2xl py-12">
    <div class="rounded-xl border border-green-200 bg-green-50 p-8 text-center dark:border-green-800 dark:bg-green-950">
        <div class="mb-4 flex justify-center">
            <flux:icon.check-circle class="size-16 text-green-600 dark:text-green-400" />
        </div>

        <flux:heading size="xl" class="mb-2 text-green-900 dark:text-green-100">Payment Successful!</flux:heading>

        <flux:text class="mb-6 text-green-800 dark:text-green-200">
            Thank you for your generous support! Your membership is being processed and will be activated shortly.
        </flux:text>

        <flux:callout variant="info" class="mb-6 text-left">
            <div class="space-y-2">
                <p class="font-semibold">What happens next?</p>
                <ul class="ml-4 list-disc space-y-1 text-sm">
                    <li>You'll receive a confirmation email with your receipt</li>
                    <li>Your membership badge will appear on your dashboard within a few moments</li>
                    <li>Your support helps us continue our mission to find loving homes for pets</li>
                </ul>
            </div>
        </flux:callout>

        <div class="flex justify-center gap-4">
            <flux:button variant="primary" href="{{ route('dashboard') }}">
                Go to Dashboard
            </flux:button>
            <flux:button variant="outline" href="{{ route('membership.manage') }}">
                View Membership
            </flux:button>
        </div>
    </div>
</div>
