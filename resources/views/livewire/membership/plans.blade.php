<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <!-- Header -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-cover bg-center p-8" style="background-image: url('{{ asset('images/default_membership.jpg') }}');">
            <div class="absolute inset-0 bg-zinc-900/45"></div>
            <div class="relative mx-auto max-w-4xl text-center">
                <flux:heading size="xl" class="mb-2 text-white">Support Our Mission</flux:heading>
                <flux:text class="text-lg text-white/90">
                    Choose a membership level to show your support. Your generous donation helps us provide better care for pets awaiting adoption.
                </flux:text>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($this->plans as $plan)
                <div class="flex flex-col rounded-xl border-2 bg-white p-8 shadow-sm dark:bg-zinc-900 {{ $plan->slug === 'gold' ? 'border-yellow-500 ring-2 ring-yellow-500 shadow-yellow-100 dark:shadow-yellow-950' : 'border-ocean-200 shadow-ocean-100 dark:border-ocean-800 dark:shadow-ocean-950' }}">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="rounded-lg p-2" style="background-color: {{ $plan->badge_color }}20">
                            <flux:icon.star class="size-6" style="color: {{ $plan->badge_color }}" />
                        </div>
                        <flux:heading size="lg">{{ $plan->name }}</flux:heading>
                    </div>

                    <flux:text class="mb-6 text-zinc-600 dark:text-zinc-400">{{ $plan->description }}</flux:text>

                    <div class="mb-6">
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-bold">${{ number_format($plan->price, 0) }}</span>
                            <span class="text-ocean-600 dark:text-ocean-400">/year</span>
                        </div>
                    </div>

                    <ul class="mb-8 space-y-3 flex-grow">
                        @foreach ($plan->features as $feature)
                            <li class="flex items-start gap-3">
                                <flux:icon.check class="size-5 text-teal-500 flex-shrink-0 mt-0.5" />
                                <flux:text>{{ $feature }}</flux:text>
                            </li>
                        @endforeach
                    </ul>

                    <div class="space-y-2">
                        <flux:button
                            href="{{ route('membership.checkout', ['plan' => $plan->slug]) }}"
                            variant="{{ $plan->slug === 'gold' ? 'primary' : 'filled' }}"
                            class="w-full"
                        >
                            Join Now
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
