@php
    $aiData = is_array($audit->ai_summary) ? $audit->ai_summary : null;
@endphp

@if ($aiData)
    <div id="section-ai" class="ff-card relative overflow-hidden">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-500 via-indigo-500 to-purple-500"></div>
        <div class="p-6">
            <h3 class="ff-section-header flex items-center gap-2 mb-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-purple-600 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </span>
                {{ __('AI Analysis') }}
                <span class="ff-badge-info">{{ __('AI') }}</span>
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">{{ __('Summary') }}</h4>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $aiData['summary'] }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">{{ __('Quality') }}</h4>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $aiData['quality'] }}</p>
                </div>
            </div>

            @if (! empty($aiData['observations']))
                <div class="mt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">{{ __('Observations') }}</h4>
                    <ul class="space-y-1.5">
                        @foreach ($aiData['observations'] as $obs)
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <span class="ff-dot-info mt-1.5 shrink-0"></span>
                                {{ $obs }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4">
                @if (! empty($aiData['risks']))
                    <div>
                        <h4 class="text-sm font-semibold text-red-600 mb-2">{{ __('Risks') }}</h4>
                        <ul class="space-y-1.5">
                            @foreach ($aiData['risks'] as $risk)
                                <li class="flex items-start gap-2 text-sm text-red-600">
                                    <span class="ff-dot-danger mt-1.5 shrink-0"></span>
                                    {{ $risk }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($aiData['recommendations']))
                    <div>
                        <h4 class="text-sm font-semibold text-brand-600 mb-2">{{ __('Recommendations') }}</h4>
                        <ul class="space-y-1.5">
                            @foreach ($aiData['recommendations'] as $rec)
                                <li class="flex items-start gap-2 text-sm text-brand-600">
                                    <span class="ff-dot mt-1.5 shrink-0 bg-brand-500"></span>
                                    {{ $rec }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            @if ($audit->model_used)
                <p class="mt-4 text-xs text-gray-400">{{ __('Model') }}: {{ $audit->model_used }} @if ($audit->tokens_used) · {{ __('Tokens') }}: {{ number_format($audit->tokens_used) }} @endif</p>
            @endif
        </div>
    </div>
@elseif ($audit->ai_summary)
    <div id="section-ai" class="ff-card relative overflow-hidden">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-500 via-indigo-500 to-purple-500"></div>
        <div class="p-6">
            <h3 class="ff-section-header flex items-center gap-2 mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-purple-600 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </span>
                {{ __('AI Summary') }}
            </h3>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $audit->ai_summary }}</p>
            @if ($audit->model_used)
                <p class="mt-4 text-xs text-gray-400">{{ __('Model') }}: {{ $audit->model_used }}</p>
            @endif
        </div>
    </div>
@endif
