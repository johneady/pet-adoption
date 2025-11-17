<div class="py-12">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
            <flux:heading size="xl" class="mb-4">Support Our Mission</flux:heading>
            <flux:text class="text-lg">
                Choose a membership level to show your support. Your generous donation helps us provide better care for pets awaiting adoption.
            </flux:text>
        </div>

        <div class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-3">
            @foreach ($this->plans as $plan)
                <div class="flex flex-col rounded-2xl border p-8 {{ $plan->slug === 'gold' ? 'border-yellow-500 ring-2 ring-yellow-500' : 'border-gray-200 dark:border-gray-700' }}">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="rounded-lg p-2" style="background-color: {{ $plan->badge_color }}20">
                            <flux:icon.{{ $plan->badge_icon }} class="size-6" style="color: {{ $plan->badge_color }}" />
                        </div>
                        <flux:heading size="lg">{{ $plan->name }}</flux:heading>
                    </div>

                    <flux:text class="mb-6">{{ $plan->description }}</flux:text>

                    <div class="mb-6">
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-bold">${{ number_format($plan->annual_price, 0) }}</span>
                            <span class="text-gray-600 dark:text-gray-400">/year</span>
                        </div>
                        <flux:text class="text-sm mt-1">
                            or ${{ number_format($plan->monthly_price, 0) }}/month
                        </flux:text>
                    </div>

                    <ul class="mb-8 space-y-3 flex-grow">
                        @foreach ($plan->features as $feature)
                            <li class="flex items-start gap-3">
                                <flux:icon.check class="size-5 text-green-500 flex-shrink-0 mt-0.5" />
                                <flux:text>{{ $feature }}</flux:text>
                            </li>
                        @endforeach
                    </ul>

                    <div class="space-y-2">
                        <flux:button
                            href="{{ route('membership.checkout', ['plan' => $plan->slug, 'type' => 'annual']) }}"
                            variant="{{ $plan->slug === 'gold' ? 'primary' : 'filled' }}"
                            class="w-full"
                        >
                            Donate Annually
                        </flux:button>
                        <flux:button
                            href="{{ route('membership.checkout', ['plan' => $plan->slug, 'type' => 'monthly']) }}"
                            variant="outline"
                            class="w-full"
                        >
                            Donate Monthly
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
