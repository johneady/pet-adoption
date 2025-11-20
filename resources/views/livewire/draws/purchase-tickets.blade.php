<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">
        <!-- Header -->
        <div class="mb-8">
            <flux:heading size="xl" class="mb-2">Purchase Tickets</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Submit a request to purchase tickets for the draw below. An administrator will process your request and register your tickets.
            </flux:text>
        </div>

        <!-- Draw Details Card -->
        <div class="mb-8 rounded-xl border border-amber-200 bg-white p-6 dark:border-amber-800 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">{{ $draw->name }}</flux:heading>

            @if ($draw->description)
                <flux:text class="mb-4 text-zinc-600 dark:text-zinc-400">{{ $draw->description }}</flux:text>
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <flux:text class="text-sm text-zinc-500">Ends</flux:text>
                    <flux:text class="font-semibold">{{ $draw->ends_at->timezone(auth()->user()?->timezone ?? 'America/Toronto')->format('M j, Y g:i A') }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Current Prize</flux:text>
                    <flux:text class="font-semibold">${{ number_format($draw->prizeAmount(), 2) }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Tickets Sold</flux:text>
                    <flux:text class="font-semibold">{{ $draw->totalTicketsSold() }}</flux:text>
                </div>
            </div>
        </div>

        <!-- Purchase Form -->
        <form wire:submit="submit">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="md" class="mb-4">Select Ticket Package</flux:heading>

                <flux:field>
                    <flux:label>Choose how many tickets you would like to purchase</flux:label>
                    <flux:select wire:model="selectedPricingTier" placeholder="Select a ticket package">
                        @foreach ($draw->ticket_price_tiers as $tier)
                            <option value="{{ json_encode($tier) }}">
                                {{ $tier['quantity'] }} ticket{{ $tier['quantity'] > 1 ? 's' : '' }} - ${{ number_format($tier['price'], 2) }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="selectedPricingTier" />
                </flux:field>

                <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950">
                    <div class="flex gap-3">
                        <flux:icon.information-circle class="size-6 shrink-0 text-blue-500" />
                        <div>
                            <flux:heading size="sm" class="text-blue-900 dark:text-blue-100">Payment Information</flux:heading>
                            <flux:text class="text-sm text-blue-700 dark:text-blue-300">
                                Online payments are not available. After submitting this request, an administrator will contact you to arrange payment and then register your tickets.
                            </flux:text>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <flux:button type="submit" variant="primary">Submit Request</flux:button>
                    <flux:button type="button" variant="ghost" href="{{ route('draws.index') }}" wire:navigate>Cancel</flux:button>
                </div>
            </div>
        </form>
    </div>
</div>
