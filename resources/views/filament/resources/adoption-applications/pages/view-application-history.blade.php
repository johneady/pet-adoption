<x-filament-panels::page>
    @php
        $statusHistory = $this->record->statusHistory()->with('changedBy')->orderBy('created_at', 'asc')->get();
    @endphp

    @if ($statusHistory->isEmpty())
        <x-filament::empty-state>
            <x-slot name="heading">
                No status history available
            </x-slot>
            <x-slot name="description">
                Status changes for this application will appear here.
            </x-slot>
            <x-slot name="icon">
                heroicon-o-clock
            </x-slot>
        </x-filament::empty-state>
    @else
        <x-filament::section>
            <x-slot name="heading">
                Status History
            </x-slot>

            <div class="fi-ta">
                <div class="fi-ta-content overflow-x-auto">
                    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
                        <thead class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">From</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">To</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Notes</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Changed by</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Date</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($statusHistory as $history)
                                <tr class="fi-ta-row">
                                    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            @if ($history->from_status)
                                                @php
                                                    $color = match($history->from_status) {
                                                        'approved', 'completed' => 'success',
                                                        'rejected' => 'danger',
                                                        'interview_scheduled' => 'warning',
                                                        'under_review' => 'info',
                                                        default => 'gray',
                                                    };
                                                @endphp
                                                <x-filament::badge :color="$color">
                                                    {{ ucwords(str_replace('_', ' ', $history->from_status)) }}
                                                </x-filament::badge>
                                            @else
                                                <span class="text-sm text-gray-400 dark:text-gray-500 italic">—</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            @php
                                                $color = match($history->to_status) {
                                                    'approved', 'completed' => 'success',
                                                    'rejected' => 'danger',
                                                    'interview_scheduled' => 'warning',
                                                    'under_review' => 'info',
                                                    default => 'gray',
                                                };
                                            @endphp
                                            <x-filament::badge :color="$color">
                                                {{ ucwords(str_replace('_', ' ', $history->to_status)) }}
                                            </x-filament::badge>
                                        </div>
                                    </td>

                                    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            @if ($history->notes)
                                                <span class="text-sm text-gray-950 dark:text-white">{{ $history->notes }}</span>
                                            @else
                                                <span class="text-sm text-gray-400 dark:text-gray-500 italic">No notes</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            @if ($history->changedBy)
                                                <div>
                                                    <div class="text-sm font-medium text-gray-950 dark:text-white">{{ $history->changedBy->name }}</div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400 dark:text-gray-500 italic">System</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            <time datetime="{{ $history->created_at }}" class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $history->created_at->format('M d, Y g:i A') }}
                                            </time>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
