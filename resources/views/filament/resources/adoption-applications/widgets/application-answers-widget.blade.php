@php
    $answers = $this->getAnswers();
@endphp

<x-filament-widgets::widget>
    <x-filament::section :collapsible="true" :collapsed="true">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-clipboard-document-list" class="h-5 w-5 text-gray-400" />
                <span>Application Responses</span>
            </div>
        </x-slot>
        @if ($answers->count() > 0)
            <div class="space-y-6">
                @foreach ($answers as $answer)
                    <div class="flex flex-col gap-1.5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $answer->question_label }}
                            @if ($answer->question_snapshot['is_required'] ?? false)
                                <span class="text-danger-500">*</span>
                            @endif
                        </dt>
                        <dd class="text-base text-gray-900 dark:text-gray-100">
                            @php
                                $type = $answer->question_type;
                                $value = $answer->answer;
                            @endphp

                            @if ($type === 'switch')
                                @if ($value === '1' || $value === 'true' || $value === true)
                                    <x-filament::badge color="success" icon="heroicon-m-check">
                                        Yes
                                    </x-filament::badge>
                                @else
                                    <x-filament::badge color="gray" icon="heroicon-m-x-mark">
                                        No
                                    </x-filament::badge>
                                @endif
                            @elseif ($value)
                                <span class="whitespace-pre-wrap">{{ $value }}</span>
                            @else
                                <span class="italic text-gray-400 dark:text-gray-500">Not provided</span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">No responses found for this application.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
