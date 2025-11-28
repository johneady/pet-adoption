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
                <div class="flex flex-col rounded-xl border-2 p-8 {{ $plan->slug === 'gold' ? 'border-yellow-500 ring-2 ring-yellow-500 bg-linear-to-br from-yellow-50 via-amber-50 to-yellow-100 dark:border-yellow-600 dark:from-yellow-950 dark:via-amber-950 dark:to-yellow-900' : 'border-ocean-200 bg-linear-to-br from-ocean-50 via-teal-50 to-ocean-100 dark:border-ocean-700 dark:from-ocean-950 dark:via-teal-950 dark:to-ocean-900' }}">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="rounded-lg p-2" style="background-color: {{ $plan->badge_color }}20">
                            <flux:icon.star class="size-6" style="color: {{ $plan->badge_color }}" />
                        </div>
                        <flux:heading size="lg" class="text-ocean-900 dark:text-ocean-100">{{ $plan->name }}</flux:heading>
                    </div>

                    <flux:text class="mb-6 text-ocean-700 dark:text-ocean-300">{{ $plan->description }}</flux:text>

                    <div class="mb-6">
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-bold text-ocean-900 dark:text-ocean-100">${{ number_format($plan->price, 0) }}</span>
                            <span class="text-ocean-600 dark:text-ocean-400">/year</span>
                        </div>
                    </div>

                    <ul class="mb-8 space-y-3 flex-grow">
                        @foreach ($plan->features as $feature)
                            <li class="flex items-start gap-3">
                                <flux:icon.check class="size-5 text-teal-500 flex-shrink-0 mt-0.5" />
                                <flux:text class="text-ocean-800 dark:text-ocean-200">{{ $feature }}</flux:text>
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
