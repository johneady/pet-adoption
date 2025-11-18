<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-2xl">
        <!-- Header -->
        <div class="mb-8 rounded-2xl bg-gradient-to-br from-ocean-50 to-teal-50 p-8 dark:from-ocean-950 dark:to-teal-950">
            <flux:heading size="xl" class="mb-2 text-ocean-900 dark:text-ocean-100">Membership</flux:heading>
            <flux:text class="text-ocean-700 dark:text-ocean-300">Your transaction was not completed</flux:text>
        </div>

        <div class="rounded-xl border-2 border-amber-200 bg-amber-50 p-8 text-center dark:border-amber-800 dark:bg-amber-950">
            <div class="mb-4 flex justify-center">
                <flux:icon.x-circle class="size-16 text-amber-600 dark:text-amber-400" />
            </div>

            <flux:heading size="xl" class="mb-2 text-amber-900 dark:text-amber-100">Payment Canceled</flux:heading>

            <flux:text class="mb-6 text-amber-800 dark:text-amber-200">
                Your payment was canceled. No charges were made to your account.
            </flux:text>

            <flux:callout variant="warning" class="mb-6 text-left">
                If you experienced any issues during checkout or have questions about membership,
                please don't hesitate to contact us. We're here to help!
            </flux:callout>

            <div class="flex justify-center gap-4">
                <flux:button variant="primary" href="{{ route('membership.plans') }}">
                    View Plans Again
                </flux:button>
                <flux:button variant="outline" href="{{ route('dashboard') }}">
                    Go to Dashboard
                </flux:button>
            </div>
        </div>
    </div>
</div>
