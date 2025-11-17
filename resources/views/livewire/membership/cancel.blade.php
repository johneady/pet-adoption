<div class="mx-auto max-w-2xl py-12">
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-8 text-center dark:border-amber-800 dark:bg-amber-950">
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
