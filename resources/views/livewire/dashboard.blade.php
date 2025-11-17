<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <flux:heading size="xl" class="mb-2">Dashboard</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">Welcome back, {{ Auth::user()->name }}!</flux:text>
        </div>

        @if (session('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @mouseenter="clearTimeout($root._x_timeout)"
                @mouseleave="$root._x_timeout = setTimeout(() => show = false, 2000)"
                class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                <flux:text class="text-green-800 dark:text-green-200">
                    {{ session('message') }}
                </flux:text>
            </div>
        @endif

        @if (Auth::user()->hasActiveMembership())
            <div class="mb-8 rounded-xl border p-6" style="border-color: {{ Auth::user()->currentMembership->plan->badge_color }}40; background: linear-gradient(135deg, {{ Auth::user()->currentMembership->plan->badge_color }}10, transparent);">
                <div class="flex items-center gap-4">
                    <div class="rounded-lg p-3" style="background-color: {{ Auth::user()->currentMembership->plan->badge_color }}20">
                        <flux:icon.star class="size-8" style="color: {{ Auth::user()->currentMembership->plan->badge_color }}" />
                    </div>
                    <div class="flex-grow">
                        <flux:heading size="lg">{{ Auth::user()->currentMembership->plan->name }} Member</flux:heading>
                        <flux:text>Thank you for supporting our mission! Your membership expires {{ Auth::user()->currentMembership->expires_at->diffForHumans() }}.</flux:text>
                    </div>
                    <flux:button href="{{ route('membership.manage') }}" variant="outline" size="sm">
                        Manage
                    </flux:button>
                </div>
            </div>
        @else
            <div class="mb-8 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg" class="mb-1">Support Our Mission</flux:heading>
                        <flux:text>Consider becoming a member to help us provide better care for pets.</flux:text>
                    </div>
                    <flux:button href="{{ route('membership.plans') }}" variant="primary">
                        View Plans
                    </flux:button>
                </div>
            </div>
        @endif

        @if ($userApplications->count() > 0)
            <div class="mb-8">
                <flux:heading size="lg" class="mb-4">Latest Applicaiton</flux:heading>

                <div class="space-y-4">
                    @foreach ($userApplications as $application)
                        <div
                            class="overflow-hidden rounded-xl border-2 border-ocean-200 bg-white dark:border-ocean-800 dark:bg-gray-900">
                            <div class="grid gap-6 p-6 md:grid-cols-[200px_1fr]">
                                <div class="overflow-hidden rounded-lg">
                                    @if ($application->pet->primaryPhoto->first())
                                        <img src="{{ Storage::url($application->pet->primaryPhoto->first()->file_path) }}"
                                            alt="{{ $application->pet->name }}" class="h-full w-full object-cover"
                                            onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div
                                            class="hidden h-48 w-full items-center justify-center bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                            <svg class="h-16 w-16 text-ocean-300 dark:text-ocean-700" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @else
                                        <div
                                            class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                            <svg class="h-16 w-16 text-ocean-300 dark:text-ocean-700" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <div class="mb-4">
                                        <div class="mb-2 flex items-center gap-3">
                                            <flux:heading size="lg">{{ $application->pet->name }}</flux:heading>
                                            @php
                                                $statusInfo =
                                                    $applicationStatuses[$application->status] ??
                                                    $applicationStatuses['submitted'];
                                                $colorClasses = [
                                                    'blue' =>
                                                        'bg-ocean-100 text-ocean-800 dark:bg-ocean-900/30 dark:text-ocean-200',
                                                    'yellow' =>
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
                                                    'purple' =>
                                                        'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200',
                                                    'green' =>
                                                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
                                                    'red' =>
                                                        'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200',
                                                    'gray' =>
                                                        'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200',
                                                ];
                                            @endphp
                                            <span
                                                class="rounded-full px-3 py-1 text-sm font-medium {{ $colorClasses[$statusInfo['color']] }}">
                                                {{ $statusInfo['label'] }}
                                            </span>
                                        </div>
                                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                                            {{ $application->pet->species->name }}@if ($application->pet->breed)
                                                , {{ $application->pet->breed->name }}
                                            @endif
                                        </flux:text>
                                    </div>

                                    <div class="space-y-3">
                                        <div>
                                            <flux:text size="sm"
                                                class="font-medium text-zinc-700 dark:text-zinc-300">
                                                Submitted:
                                            </flux:text>
                                            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                                                {{ $application->created_at->format('M j, Y \a\t g:i A') }}
                                            </flux:text>
                                        </div>

                                        @if ($application->interview)
                                            <div
                                                class="rounded-lg border border-purple-200 bg-purple-50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                                                <flux:text size="sm"
                                                    class="mb-2 font-medium text-purple-900 dark:text-purple-100">
                                                    Interview Details
                                                </flux:text>
                                                <div class="space-y-1">
                                                    <flux:text size="sm"
                                                        class="text-purple-800 dark:text-purple-200">
                                                        <strong>Date:</strong>
                                                        {{ $application->interview->scheduled_at->format('M j, Y \a\t g:i A') }}
                                                    </flux:text>
                                                    @if ($application->interview->location)
                                                        <flux:text size="sm"
                                                            class="text-purple-800 dark:text-purple-200">
                                                            <strong>Location:</strong>
                                                            {{ $application->interview->location }}
                                                        </flux:text>
                                                    @endif
                                                    @if ($application->interview->completed_at)
                                                        <flux:text size="sm"
                                                            class="text-purple-800 dark:text-purple-200">
                                                            <strong>Status:</strong> Completed
                                                        </flux:text>
                                                    @else
                                                        <flux:text size="sm"
                                                            class="text-purple-800 dark:text-purple-200">
                                                            <strong>Status:</strong> Scheduled
                                                        </flux:text>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <div>
                                            <flux:text size="sm"
                                                class="font-medium text-zinc-700 dark:text-zinc-300">
                                                Application ID:
                                            </flux:text>
                                            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                                                #{{ $application->id }}
                                            </flux:text>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border-2 border-ocean-200 bg-white p-6 dark:border-ocean-800 dark:bg-gray-900">
                <flux:heading size="lg" class="mb-4">Adoption Process</flux:heading>
                <flux:text class="mb-6 text-zinc-600 dark:text-zinc-400">
                    Track your application progress through our adoption workflow. We'll keep you updated at each step.
                </flux:text>

                <div class="space-y-4">
                    @php
                        $processSteps = [
                            'submitted' => ['label' => 'Submitted', 'icon' => 'check-circle'],
                            'interview_scheduled' => ['label' => 'Interview', 'icon' => 'calendar'],
                            'under_review' => ['label' => 'Under Review', 'icon' => 'eye'],
                            'final' => ['label' => 'Final Decision', 'icon' => 'check-circle'],
                        ];

                        $statusOrder = ['submitted', 'interview_scheduled', 'under_review', 'approved', 'rejected'];
                        $currentIndex = array_search($currentStatus, $statusOrder);
                        if ($currentIndex === false) {
                            $currentIndex = 0;
                        }

                        // Map approved/rejected to the final step for display purposes
                        $displayStatus = in_array($currentStatus, ['approved', 'rejected']) ? 'final' : $currentStatus;
                    @endphp

                    @foreach ($processSteps as $stepStatus => $step)
                        @php
                            // Determine step completion status
                            if ($stepStatus === 'final') {
                                // Final step is current if status is approved or rejected
                                $isCompleted = false;
                                $isCurrent = in_array($currentStatus, ['approved', 'rejected']);
                                $isUpcoming = !$isCurrent && $currentIndex < 3;
                            } else {
                                $stepIndex = array_search($stepStatus, $statusOrder);
                                $isCompleted = $stepIndex < $currentIndex;
                                $isCurrent = $stepIndex === $currentIndex;
                                $isUpcoming = $stepIndex > $currentIndex;
                            }
                        @endphp

                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full
                                @if ($isCompleted) bg-green-100 dark:bg-green-900/30
                                @elseif($isCurrent && $currentStatus === 'approved') bg-green-100 dark:bg-green-900/30
                                @elseif($isCurrent && $currentStatus === 'rejected') bg-red-100 dark:bg-red-900/30
                                @elseif($isCurrent) bg-ocean-100 dark:bg-ocean-900/30
                                @else bg-ocean-50 dark:bg-ocean-950 @endif">
                                @if ($isCompleted || ($isCurrent && $currentStatus === 'approved'))
                                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                @elseif($isCurrent && $currentStatus === 'rejected')
                                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                @elseif($isCurrent)
                                    <svg class="h-6 w-6 text-ocean-600 dark:text-ocean-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <div class="h-4 w-4 rounded-full bg-ocean-300 dark:bg-ocean-700"></div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <flux:text
                                    class="font-medium
                                    @if ($isCompleted) text-green-900 dark:text-green-100
                                    @elseif($isCurrent && $currentStatus === 'approved') text-green-900 dark:text-green-100
                                    @elseif($isCurrent && $currentStatus === 'rejected') text-red-900 dark:text-red-100
                                    @elseif($isCurrent) text-ocean-900 dark:text-ocean-100
                                    @else text-ocean-400 dark:text-ocean-600 @endif">
                                    {{ $step['label'] }}
                                    @if ($stepStatus === 'final' && $isCurrent)
                                        - {{ ucfirst($currentStatus) }}
                                    @endif
                                </flux:text>
                                @if ($isCurrent && isset($applicationStatuses[$currentStatus]))
                                    @php
                                        $timestamp = $this->getStatusTimestamp(
                                            $userApplications->first(),
                                            $currentStatus,
                                        );
                                    @endphp
                                    <flux:text size="sm"
                                        class="
                                        @if ($currentStatus === 'approved') text-green-700 dark:text-green-300
                                        @elseif($currentStatus === 'rejected') text-red-700 dark:text-red-300
                                        @else text-ocean-700 dark:text-ocean-300 @endif">
                                        {{ $applicationStatuses[$currentStatus]['description'] }}@if ($timestamp)
                                            ({{ $timestamp }})
                                        @endif
                                    </flux:text>
                                @endif
                            </div>
                        </div>

                        @if (!$loop->last)
                            <div
                                class="ml-5 h-6 w-0.5
                                @if ($isCompleted) bg-green-300 dark:bg-green-700
                                @else bg-ocean-200 dark:bg-ocean-800 @endif">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div
                class="flex min-h-[400px] items-center justify-center rounded-xl border-2 border-ocean-200 bg-gradient-to-br from-ocean-50 to-teal-50 p-12 dark:border-ocean-800 dark:from-ocean-950 dark:to-teal-950">
                <div class="text-center">
                    <svg class="mx-auto h-24 w-24 text-ocean-300 dark:text-ocean-700" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <flux:heading size="lg" class="mb-2 mt-4 text-ocean-900 dark:text-ocean-100">No Applications Yet</flux:heading>
                    <flux:text class="mb-4 text-ocean-700 dark:text-ocean-300">
                        You haven't submitted any adoption applications. Ready to find your perfect companion?
                    </flux:text>
                    <flux:button href="{{ route('pets.index') }}" wire:navigate variant="primary">
                        Browse Our Pets
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</div>
