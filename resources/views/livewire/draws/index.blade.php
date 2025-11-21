<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <!-- Header -->
        <div class="mb-8 rounded-2xl bg-gradient-to-br from-ocean-50 to-teal-50 p-8 dark:from-ocean-950 dark:to-teal-950">
            <div class="mx-auto max-w-4xl text-center">
                <flux:heading size="xl" class="mb-2 text-ocean-900 dark:text-ocean-100">50/50 Draws</flux:heading>
                <flux:text class="text-lg text-ocean-700 dark:text-ocean-300">
                    Support our shelter animals while having a chance to win! Half of all proceeds go to animal care and the other half goes to one lucky winner.
                </flux:text>
            </div>
        </div>

        <!-- Compliance Notice -->
        <div class="mb-8 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950">
            <div class="flex gap-3">
                <flux:icon.information-circle class="size-6 flex-shrink-0 text-blue-500" />
                <div>
                    <flux:heading size="sm" class="text-blue-900 dark:text-blue-100">Payment Information</flux:heading>
                    <flux:text class="text-sm text-blue-700 dark:text-blue-300">
                        To comply with applicable laws, online payments are currently not available for ticket purchases. Please contact us directly to purchase tickets. Once your purchase is registered by an administrator, your tickets will be displayed on your profile.
                    </flux:text>
                </div>
            </div>
        </div>

        <!-- Active Draw -->
        @if ($this->activeDraw)
            <div class="mb-8">
                <flux:heading size="lg" class="mb-4">Current Draw</flux:heading>
                <div class="rounded-xl border-2 border-ocean-300 bg-white p-6 shadow-lg shadow-ocean-200/50 dark:border-ocean-700 dark:bg-zinc-900 dark:shadow-ocean-900/50">
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <flux:heading size="xl">{{ $this->activeDraw->name }}</flux:heading>
                            <flux:badge color="success" class="mt-2">Active</flux:badge>
                        </div>
                        <div class="text-right">
                            <flux:text class="text-sm text-zinc-500">Ends</flux:text>
                            <flux:text class="font-semibold">{{ $this->activeDraw->ends_at->timezone(auth()->user()?->timezone ?? 'America/Toronto')->format('M j, Y') }}</flux:text>
                        </div>
                    </div>

                    @if ($this->activeDraw->description)
                        <flux:text class="mb-6 text-zinc-600 dark:text-zinc-400">{{ $this->activeDraw->description }}</flux:text>
                    @endif

                    <!-- Ticket Pricing -->
                    <div class="mb-6">
                        <flux:heading size="sm" class="mb-3">Ticket Pricing</flux:heading>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($this->activeDraw->ticket_price_tiers as $tier)
                                <div class="rounded-lg border border-ocean-200 bg-ocean-50 px-4 py-2 dark:border-ocean-800 dark:bg-ocean-950">
                                    <span class="font-semibold">{{ $tier['quantity'] }} ticket{{ $tier['quantity'] > 1 ? 's' : '' }}</span>
                                    <span class="text-ocean-700 dark:text-ocean-300">for ${{ number_format($tier['price'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                        <div class="rounded-lg bg-zinc-100 p-3 text-center dark:bg-zinc-800">
                            <flux:text class="text-sm text-zinc-500">Tickets Sold</flux:text>
                            <flux:text class="text-2xl font-bold">{{ $this->activeDraw->totalTicketsSold() }}</flux:text>
                        </div>
                        <div class="rounded-lg bg-zinc-100 p-3 text-center dark:bg-zinc-800">
                            <flux:text class="text-sm text-zinc-500">Current Prize</flux:text>
                            <flux:text class="text-2xl font-bold">${{ number_format($this->activeDraw->prizeAmount(), 2) }}</flux:text>
                        </div>
                        <div class="rounded-lg bg-zinc-100 p-3 text-center dark:bg-zinc-800">
                            <flux:text class="text-sm text-zinc-500">Days Remaining</flux:text>
                            <flux:text class="text-2xl font-bold">{{ (int) now()->diffInDays($this->activeDraw->ends_at) }}</flux:text>
                        </div>
                        @auth
                            <div class="rounded-lg bg-ocean-100 p-3 text-center dark:bg-ocean-900">
                                <flux:text class="text-sm text-ocean-700 dark:text-ocean-300">Your Tickets</flux:text>
                                <flux:text class="text-2xl font-bold text-ocean-900 dark:text-ocean-100">{{ $this->userTickets->count() }}</flux:text>
                            </div>
                        @endauth
                    </div>

                    <!-- User's Tickets -->
                    @auth
                        @if ($this->userTickets->count() > 0)
                            <div class="mt-6">
                                <flux:heading size="sm" class="mb-3">Your Ticket Numbers</flux:heading>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($this->userTickets as $ticket)
                                        <flux:badge color="info">#{{ $ticket->ticket_number }}</flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Purchase Tickets Button -->
                        <div class="mt-6">
                            <flux:button variant="primary" :href="route('draws.purchase', ['draw' => $this->activeDraw->id])" wire:navigate>
                                Purchase Tickets
                            </flux:button>
                        </div>
                    @endauth

                    @guest
                        <!-- Login Prompt for Guests -->
                        <div class="mt-6 rounded-lg border border-ocean-200 bg-ocean-50 p-4 dark:border-ocean-800 dark:bg-ocean-950">
                            <div class="flex items-center justify-between gap-4">
                                <flux:text class="text-ocean-900 dark:text-ocean-100">
                                    Log in to purchase tickets for this draw
                                </flux:text>
                                <flux:button variant="primary" :href="route('login')" size="sm">
                                    Log In
                                </flux:button>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        @endif

        <!-- Upcoming Draws -->
        @if ($this->upcomingDraws->count() > 0)
            <div class="mb-8">
                <flux:heading size="lg" class="mb-4">Upcoming Draws</flux:heading>
                <div class="space-y-4">
                    @foreach ($this->upcomingDraws as $draw)
                        <div class="rounded-xl border-2 border-ocean-200 bg-white p-6 shadow-sm shadow-ocean-100 dark:border-ocean-800 dark:bg-zinc-900 dark:shadow-ocean-950">
                            <div class="flex items-start justify-between">
                                <div>
                                    <flux:heading size="md">{{ $draw->name }}</flux:heading>
                                    <flux:badge color="info" class="mt-2">Coming Soon</flux:badge>
                                </div>
                                <div class="text-right">
                                    <flux:text class="text-sm text-zinc-500">Starts</flux:text>
                                    <flux:text class="font-semibold">{{ $draw->starts_at->timezone(auth()->user()?->timezone ?? 'America/Toronto')->format('M j, Y') }}</flux:text>
                                </div>
                            </div>
                            @if ($draw->description)
                                <flux:text class="mt-3 text-zinc-600 dark:text-zinc-400">{{ $draw->description }}</flux:text>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Past Draws -->
        @if ($this->pastDraws->count() > 0)
            <div>
                <flux:heading size="lg" class="mb-4">Past Draws</flux:heading>
                <div class="space-y-4">
                    @foreach ($this->pastDraws as $draw)
                        <div class="rounded-xl border-2 border-ocean-200 bg-white p-6 shadow-sm shadow-ocean-100 dark:border-ocean-800 dark:bg-zinc-900 dark:shadow-ocean-950">
                            <div class="flex items-start justify-between">
                                <div>
                                    <flux:heading size="md">{{ $draw->name }}</flux:heading>
                                    <flux:badge color="gray" class="mt-2">Completed</flux:badge>
                                </div>
                                <div class="text-right">
                                    <flux:text class="text-sm text-zinc-500">Ended</flux:text>
                                    <flux:text class="font-semibold">{{ $draw->ends_at->timezone(auth()->user()?->timezone ?? 'America/Toronto')->format('M j, Y') }}</flux:text>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
                                <div>
                                    <flux:text class="text-sm text-zinc-500">Winner</flux:text>
                                    <flux:text class="font-semibold">{{ $draw->winnerTicket?->user?->firstName() ?? 'N/A' }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-sm text-zinc-500">Winning Ticket</flux:text>
                                    <flux:text class="font-semibold">#{{ $draw->winnerTicket?->ticket_number ?? 'N/A' }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-sm text-zinc-500">Total Tickets</flux:text>
                                    <flux:text class="font-semibold">{{ $draw->totalTicketsSold() }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-sm text-zinc-500">Prize Amount</flux:text>
                                    <flux:text class="font-semibold">${{ number_format($draw->prizeAmount(), 2) }}</flux:text>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- No Draws Message -->
        @if (!$this->activeDraw && $this->upcomingDraws->count() === 0 && $this->pastDraws->count() === 0)
            <div class="rounded-xl border-2 border-ocean-200 bg-gradient-to-br from-ocean-50 to-teal-50 p-12 text-center dark:border-ocean-800 dark:from-ocean-950 dark:to-zinc-900">
                <flux:icon.ticket class="mx-auto mb-4 size-12 text-ocean-300 dark:text-ocean-700" />
                <flux:heading size="md" class="mb-2 text-ocean-900 dark:text-ocean-100">No Draws Available</flux:heading>
                <flux:text class="text-ocean-700 dark:text-ocean-300">Check back soon for upcoming 50/50 draws!</flux:text>
            </div>
        @endif
    </div>
</div>
