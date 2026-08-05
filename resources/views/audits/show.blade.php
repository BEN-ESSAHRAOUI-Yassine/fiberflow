<x-app-layout>
    <x-slot name="header">
        <div class="ff-page-header-actions">
            <div>
                <div class="ff-breadcrumb">
                    <a href="{{ route('admin.projects.show', $project) }}">{{ $project->name }}</a>
                    <span class="ff-breadcrumb-sep">/</span>
                    <a href="{{ route('admin.projects.audits.index', $project) }}">{{ __('Audits') }}</a>
                    <span class="ff-breadcrumb-sep">/</span>
                    <span class="text-gray-900">#{{ $audit->id }}</span>
                </div>
                <div class="flex items-center gap-3 mt-1">
                    <h1 class="ff-page-title text-2xl">{{ __('Audit') }} #{{ $audit->id }}</h1>
                    <span class="ff-badge-lg
                        @switch($audit->status->value)
                            @case('completed') bg-emerald-50 text-emerald-700 @break
                            @case('running') bg-brand-50 text-brand-700 @break
                            @case('pending') bg-amber-50 text-amber-700 @break
                            @case('failed') bg-red-50 text-red-700 @break
                        @endswitch
                    ">
                        <span class="ff-dot
                            @switch($audit->status->value)
                                @case('completed') ff-dot-success @break
                                @case('running') ff-dot-info @break
                                @case('pending') ff-dot-warning @break
                                @case('failed') ff-dot-danger @break
                            @endswitch
                        "></span>
                        {{ ucfirst($audit->status->value) }}
                    </span>
                </div>
                <div class="ff-pills mt-2">
                    <span class="ff-pill">{{ $audit->project_type_at_audit }}</span>
                    <span class="ff-pill">{{ $audit->phase_at_audit }}</span>
                    <span class="ff-pill">{{ __('by') }} {{ $audit->performer?->name ?? $audit->performed_by }}</span>
                    @if ($audit->completed_at)
                        <span class="ff-pill">{{ $audit->completed_at->format('M j, Y g:i A') }}</span>
                    @endif
                </div>
            </div>
            @if ($audit->status->value === 'completed')
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.projects.audits.pdf', [$project, $audit]) }}" class="ff-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('PDF') }}
                    </a>
                    <a href="{{ route('admin.projects.audits.excel', [$project, $audit]) }}" class="ff-btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('Excel') }}
                    </a>
                </div>
            @endif
            @if ($audit->status->value === 'failed')
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('admin.projects.audits.retry', [$project, $audit]) }}">
                        @csrf
                        <button type="submit" class="ff-btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            {{ __('Retry') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    @if ($audit->error_message)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ $audit->error_message }}
            </div>
        </div>
    @endif

    @if ($audit->status->value === 'completed')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Score Hero --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <div class="lg:col-span-2 ff-card">
                    <div class="p-6 flex flex-col items-center justify-center">
                        @php $score = round($audit->quality_score, 1); @endphp
                        <div class="ff-gauge w-36 h-36 mb-2">
                            <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="42" fill="none" stroke="#E5E7EB" stroke-width="8"/>
                                <circle cx="50" cy="50" r="42" fill="none" stroke-width="8" stroke-linecap="round"
                                    stroke-dasharray="{{ $score * 2.64 }}"
                                    stroke-dashoffset="0"
                                    class="@if ($score >= 90) ff-score-excellent
                                    @elseif ($score >= 75) ff-score-good
                                    @elseif ($score >= 50) ff-score-acceptable
                                    @else ff-score-poor @endif"
                                    style="transition: stroke-dasharray 1s ease-in-out;"/>
                            </svg>
                            <div class="ff-gauge-value">{{ $score }}</div>
                            <div class="ff-gauge-label text-gray-400">/100</div>
                        </div>
                        <div class="text-sm font-medium text-gray-500">{{ __('Overall Score') }}</div>
                        <div class="mt-1 text-sm font-semibold
                            @if ($score >= 90) ff-score-excellent
                            @elseif ($score >= 75) ff-score-good
                            @elseif ($score >= 50) ff-score-acceptable
                            @else ff-score-poor @endif
                        ">
                            @if ($score >= 90) {{ __('Excellent') }}
                            @elseif ($score >= 75) {{ __('Good') }}
                            @elseif ($score >= 50) {{ __('Acceptable') }}
                            @else {{ __('Non-compliant') }} @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3 ff-card">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('Score Breakdown') }}</h3>
                        <div class="space-y-4">
                            @php
                                $scoreItems = [
                                    ['label' => __('Connectivity'), 'value' => $audit->connectivity_score, 'weight' => '40%'],
                                    ['label' => __('Coherence'), 'value' => $audit->coherence_score, 'weight' => '30%'],
                                    ['label' => __('Capacity'), 'value' => $audit->capacity_score, 'weight' => '20%'],
                                    ['label' => __('Extensibility'), 'value' => $audit->extensibility_score, 'weight' => '10%'],
                                ];
                            @endphp
                            @foreach ($scoreItems as $item)
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
                                        <span class="text-sm font-semibold
                                            @if ($item['value'] >= 90) ff-score-excellent
                                            @elseif ($item['value'] >= 75) ff-score-good
                                            @elseif ($item['value'] >= 50) ff-score-acceptable
                                            @else ff-score-poor @endif
                                        ">{{ number_format($item['value'], 1) }}</span>
                                    </div>
                                    <div class="w-full bg-surface-100 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-500
                                            @if ($item['value'] >= 90) bg-emerald-500
                                            @elseif ($item['value'] >= 75) bg-brand-500
                                            @elseif ($item['value'] >= 50) bg-amber-500
                                            @else bg-red-500 @endif
                                        " style="width: {{ $item['value'] }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ __('Weight') }}: {{ $item['weight'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            @php
                $anomalyCount = $audit->anomaly_count;
                $criticalCount = $audit->critical_anomaly_count;
                $ns = $audit->network_statistics;
                $detailed = $ns['detailed'] ?? null;
                $cableCount = $detailed['cables']['total_count'] ?? 0;
                $cableLength = $detailed['cables']['total_length_m'] ?? 0;
                $boxCount = $detailed['equipment']['optical_boxes']['total'] ?? 0;
                $supportCount = $detailed['supports']['technical_points']['total'] ?? 0;
                $layerCount = collect($ns)->except(['total_fibers', 'used_fibers', 'spare_fibers', 'occupation_rate', 'detailed'])->count();
                $fpb = $detailed['fibers_per_pbo'] ?? [];
                $pboCount = $fpb['pbo_count'] ?? 0;
                $totalCapa = array_sum(array_column($fpb['feeder_cables'] ?? [], 'capacity'));
                $totalUtile = $fpb['total_fiber_utile'] ?? 0;
                $occRate = $totalCapa > 0 ? round($totalUtile / $totalCapa * 100) : 0;
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="ff-stat-card cursor-pointer hover:border-brand-300 transition-colors" onclick="document.getElementById('section-anomalies').scrollIntoView({behavior: 'smooth'})">
                    <div class="ff-stat-card-icon bg-amber-50">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="ff-stat-card-value mt-3">{{ number_format($anomalyCount) }}</div>
                    <div class="ff-stat-card-label">{{ __('Anomalies') }} · <span class="text-red-600">{{ $criticalCount }} {{ __('critical') }}</span></div>
                </div>

                <div class="ff-stat-card">
                    <div class="ff-stat-card-icon bg-brand-50">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 3.5 4 8 4s8-2 8-4V7M4 7c0 2 3.5 4 8 4s8-2 8-4M4 7c0-2 3.5-4 8-4s8 2 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                    </div>
                    <div class="ff-stat-card-value mt-3">{{ $layerCount }}</div>
                    <div class="ff-stat-card-label">{{ __('Layers') }}</div>
                </div>

                <div class="ff-stat-card">
                    <div class="ff-stat-card-icon bg-emerald-50">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="ff-stat-card-value mt-3">{{ $cableCount }}</div>
                    <div class="ff-stat-card-label">{{ __('Cables') }} · {{ number_format($cableLength / 1000, 1) }} km</div>
                </div>

                <div class="ff-stat-card">
                    <div class="ff-stat-card-icon bg-purple-50">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div class="ff-stat-card-value mt-3">{{ $boxCount }}</div>
                    <div class="ff-stat-card-label">{{ __('Optical Boxes') }}</div>
                </div>
            </div>

            {{-- AI Analysis --}}
            @php $aiData = is_array($audit->ai_summary) ? $audit->ai_summary : null; @endphp
            @if ($aiData)
                <div class="ff-card border-l-2 border-l-brand-500">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('AI Analysis') }}</h3>
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
                <div class="ff-card border-l-2 border-l-brand-500">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-2">{{ __('AI Summary') }}</h3>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $audit->ai_summary }}</p>
                        @if ($audit->model_used)
                            <p class="mt-4 text-xs text-gray-400">{{ __('Model') }}: {{ $audit->model_used }}</p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Anomalies --}}
            <div id="section-anomalies" class="ff-card">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="ff-section-header">{{ __('Anomalies') }}</h3>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="ff-dot ff-dot-warning"></span>
                                <span class="text-amber-600 font-semibold">{{ $anomalyCount }}</span>
                                <span class="text-gray-400">{{ __('warnings') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="ff-dot ff-dot-danger"></span>
                                <span class="text-red-600 font-semibold">{{ $criticalCount }}</span>
                                <span class="text-gray-400">{{ __('critical') }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($ns)
                        @php
                            $severityOrder = ['critical' => 0, 'warning' => 1, 'info' => 2];
                            $anomalies = collect($ns['detailed']['anomalies'] ?? [])
                                ->sortBy(fn($a) => $severityOrder[$a['severity']] ?? 99);
                        @endphp

                        @if ($anomalies->isNotEmpty())
                            <div x-data="paginate(@js($anomalies->values()->all()), 10)">
                                <div class="overflow-x-auto">
                                    <table class="ff-table text-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-left">{{ __('SHP') }}</th>
                                                <th class="text-left">{{ __('Type') }}</th>
                                                <th class="text-left">{{ __('Message') }}</th>
                                                <th class="text-left">{{ __('Solution') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(a, index) in paginatedItems" :key="index">
                                                <tr>
                                                    <td class="text-gray-500 font-mono text-xs" x-text="a.shp || '-'"></td>
                                                    <td>
                                                        <span class="inline-flex items-center gap-1">
                                                            <span class="ff-dot" :class="{
                                                                'ff-dot-danger': a.severity === 'critical',
                                                                'ff-dot-warning': a.severity === 'warning',
                                                                'ff-dot-info': a.severity !== 'critical' && a.severity !== 'warning'
                                                            }"></span>
                                                            <span class="text-gray-700" x-text="({
                                                                transport: 'Transport',
                                                                distribution: 'Distribution',
                                                                cable: 'Câble',
                                                                ebp: 'Boîte (EBP)',
                                                                fiber_saturation: 'Saturation fibre',
                                                                fiber_no_feeder: 'Zone non desservie'
                                                            })[a.type] || a.type"></span>
                                                        </span>
                                                    </td>
                                                    <td class="text-gray-700" x-text="a.message"></td>
                                                    <td class="text-gray-500" x-text="a.solution || '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                <div x-show="totalPages > 1" class="flex items-center justify-between mt-4 px-2">
                                    <span class="text-sm text-gray-500">
                                        <span x-text="totalItems"></span> {{ __('results') }} · {{ __('Page') }}
                                        <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                                    </span>
                                    <div class="flex gap-2">
                                        <button @click="prev()" :disabled="currentPage <= 1"
                                            class="ff-btn-ghost text-sm disabled:opacity-40 disabled:cursor-not-allowed">
                                            &laquo; {{ __('Prev') }}
                                        </button>
                                        <button @click="next()" :disabled="currentPage >= totalPages"
                                            class="ff-btn-ghost text-sm disabled:opacity-40 disabled:cursor-not-allowed">
                                            {{ __('Next') }} &raquo;
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-8">{{ __('No anomalies found.') }}</p>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Accordion Sections --}}
            @if ($ns && $detailed)
                <div class="space-y-4" x-data="{ openSections: ['layers'] }">

                    {{-- Layer Overview --}}
                    @php $layers = collect($ns)->except(['total_fibers', 'used_fibers', 'spare_fibers', 'occupation_rate', 'detailed']); @endphp
                    @if ($layers->isNotEmpty())
                        <div class="ff-accordion">
                            <button @click="openSections.includes('layers') ? openSections = openSections.filter(s => s !== 'layers') : openSections.push('layers')"
                                class="ff-accordion-trigger">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 3.5 4 8 4s8-2 8-4V7M4 7c0 2 3.5 4 8 4s8-2 8-4M4 7c0-2 3.5-4 8-4s8 2 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                                    <span class="ff-section-header">{{ __('Layer Overview') }}</span>
                                    <span class="ff-badge-neutral">{{ $layers->count() }} {{ __('layers') }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openSections.includes('layers') }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openSections.includes('layers')" x-collapse x-cloak>
                                <div class="ff-accordion-content">
                                    <div class="overflow-x-auto">
                                        <table class="ff-table text-sm">
                                            <thead>
                                                <tr>
                                                    <th class="text-left">{{ __('Layer') }}</th>
                                                    <th class="text-right">{{ __('Count') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($layers as $layer => $count)
                                                    <tr>
                                                        <td class="text-gray-900 font-mono text-xs">{{ $layer }}</td>
                                                        <td class="text-right text-gray-500">{{ number_format($count) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Cables --}}
                    @if (!empty($detailed['cables']['total_count']))
                        <div class="ff-accordion">
                            <button @click="openSections.includes('cables') ? openSections = openSections.filter(s => s !== 'cables') : openSections.push('cables')"
                                class="ff-accordion-trigger">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span class="ff-section-header">{{ __('Cables') }}</span>
                                    <span class="ff-badge-neutral">{{ $detailed['cables']['total_count'] }} · {{ number_format($detailed['cables']['total_length_m'] / 1000, 2) }} km</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openSections.includes('cables') }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openSections.includes('cables')" x-collapse x-cloak>
                                <div class="ff-accordion-content">
                                    @if (!empty($detailed['cables']['by_reference']))
                                        @php $cablesByRef = collect($detailed['cables']['by_reference'])->sortByDesc('count')->values()->all(); @endphp
                                        <div x-data="paginate(@js($cablesByRef), 10)">
                                            <div class="overflow-x-auto">
                                                <table class="ff-table text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-left">{{ __('Designation') }}</th>
                                                            <th class="text-left">{{ __('Manufacturer') }}</th>
                                                            <th class="text-center">{{ __('FO') }}</th>
                                                            <th class="text-center">{{ __('Mod.') }}</th>
                                                            <th class="text-right">{{ __('Count') }}</th>
                                                            <th class="text-right">{{ __('Carto (m)') }}</th>
                                                            <th class="text-right">{{ __('Adj. (m)') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template x-for="(ref, index) in paginatedItems" :key="index">
                                                            <tr>
                                                                <td class="max-w-xs">
                                                                    <div class="truncate font-medium text-sm text-gray-900" x-text="ref.designation || '-'"></div>
                                                                    <div class="text-xs text-gray-400 font-mono" x-text="ref.rf_code"></div>
                                                                </td>
                                                                <td class="text-gray-700" x-text="ref.manufacturer"></td>
                                                                <td class="text-center text-gray-500" x-text="ref.fiber_count || '-'"></td>
                                                                <td class="text-center text-gray-500" x-text="ref.modulo || '-'"></td>
                                                                <td class="text-right text-gray-500" x-text="ref.count"></td>
                                                                <td class="text-right text-gray-500" x-text="ref.carto_length_m?.toFixed(1)"></td>
                                                                <td class="text-right text-gray-500" x-text="ref.adjusted_length_m?.toFixed(1)"></td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div x-show="totalPages > 1" class="flex items-center justify-between mt-4 px-2">
                                                <span class="text-sm text-gray-500"><span x-text="totalItems"></span> {{ __('results') }}</span>
                                                <div class="flex gap-2">
                                                    <button @click="prev()" :disabled="currentPage <= 1" class="ff-btn-ghost text-sm disabled:opacity-40">&laquo;</button>
                                                    <span class="text-sm text-gray-500"><span x-text="currentPage"></span>/<span x-text="totalPages"></span></span>
                                                    <button @click="next()" :disabled="currentPage >= totalPages" class="ff-btn-ghost text-sm disabled:opacity-40">&raquo;</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Fiber Usage --}}
                    @if (!empty($detailed['fibers']) || !empty($fpb['feeder_cables']))
                        <div class="ff-accordion">
                            <button @click="openSections.includes('fibers') ? openSections = openSections.filter(s => s !== 'fibers') : openSections.push('fibers')"
                                class="ff-accordion-trigger">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 3.5 4 8 4s8-2 8-4V7M4 7c0 2 3.5 4 8 4s8-2 8-4M4 7c0-2 3.5-4 8-4s8 2 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                                    <span class="ff-section-header">{{ __('Fiber Usage') }}</span>
                                    <span class="ff-badge-neutral">{{ $occRate }}% {{ __('occupation') }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openSections.includes('fibers') }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openSections.includes('fibers')" x-collapse x-cloak>
                                <div class="ff-accordion-content">
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                                        <div class="ff-card p-3">
                                            <div class="text-lg font-semibold text-emerald-600">{{ $totalCapa }}</div>
                                            <div class="text-xs text-emerald-600">{{ __('Capacity') }}</div>
                                        </div>
                                        <div class="ff-card p-3">
                                            <div class="text-lg font-semibold text-brand-600">{{ $totalUtile }}</div>
                                            <div class="text-xs text-brand-600">{{ __('Used') }}</div>
                                        </div>
                                        <div class="ff-card p-3">
                                            <div class="text-lg font-semibold text-amber-600">{{ $fpb['total_fiber_disponible'] ?? 0 }}</div>
                                            <div class="text-xs text-amber-600">{{ __('Reserve') }}</div>
                                        </div>
                                        <div class="ff-card p-3">
                                            <div class="text-lg font-semibold text-gray-900">{{ $pboCount }}</div>
                                            <div class="text-xs text-gray-500">{{ __('PBO Zones') }}</div>
                                        </div>
                                    </div>

                                    @if (!empty($fpb['feeder_cables']))
                                        <div x-data="{ openFeeder: null }" class="space-y-2">
                                            @foreach ($fpb['feeder_cables'] as $idx => $feeder)
                                                <div class="ff-card overflow-hidden">
                                                    <button @click="openFeeder === {{ $idx }} ? openFeeder = null : openFeeder = {{ $idx }}"
                                                        class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-surface-50 transition-colors">
                                                        <div class="flex items-center gap-3">
                                                            <svg class="w-4 h-4 text-brand-600 transition-transform" :class="{ 'rotate-90': openFeeder === {{ $idx }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                            <span class="font-mono text-sm font-semibold text-gray-900">{{ $feeder['cable_code'] }}</span>
                                                            <span class="text-xs text-gray-400">
                                                                {{ __('Cap') }}: {{ $feeder['capacity'] }} · {{ __('Used') }}: {{ $feeder['total_utile'] }} · {{ __('Avail') }}: {{ $feeder['total_disponible'] }}
                                                            </span>
                                                        </div>
                                                    </button>
                                                    <div x-show="openFeeder === {{ $idx }}" x-collapse x-cloak>
                                                        @if (!empty($feeder['zones']))
                                                            <div class="px-4 pb-3">
                                                                <table class="ff-table text-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-left">{{ __('PBO Zone') }}</th>
                                                                            <th class="text-right">{{ __('Prises') }}</th>
                                                                            <th class="text-right">{{ __('Used') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($feeder['zones'] as $zone)
                                                                            <tr>
                                                                                <td class="text-gray-900 font-mono text-xs">{{ $zone['zp_code'] }}</td>
                                                                                <td class="text-right text-gray-500">{{ $zone['prises'] }}</td>
                                                                                <td class="text-right text-gray-700 font-medium">{{ $zone['fiber_utile'] }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Operations --}}
                    @if (!empty($fpb['operations_chantier']['epissurages']))
                        <div class="ff-accordion">
                            <button @click="openSections.includes('ops') ? openSections = openSections.filter(s => s !== 'ops') : openSections.push('ops')"
                                class="ff-accordion-trigger">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="ff-section-header">{{ __('Site Operations') }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openSections.includes('ops') }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openSections.includes('ops')" x-collapse x-cloak>
                                <div class="ff-accordion-content">
                                    @foreach ($fpb['operations_chantier']['epissurages'] as $ep)
                                        <div class="mb-4 pb-4 border-b border-surface-100 last:border-b-0 last:mb-0 last:pb-0">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('Cable') }}: <span class="font-mono">{{ $ep['cable_code'] }}</span>
                                                <span class="text-xs text-gray-400 font-normal">({{ __('Capacity') }}: {{ $ep['capacity'] }}, {{ __('Length') }}: {{ number_format($ep['length_m'], 1) }} m)</span>
                                            </h4>
                                            @if (!empty($ep['points']))
                                                <div class="overflow-x-auto">
                                                    <table class="ff-table text-sm">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-left">{{ __('Box') }}</th>
                                                                <th class="text-left">{{ __('Type') }}</th>
                                                                <th class="text-left">{{ __('Zone') }}</th>
                                                                <th class="text-right">{{ __('Distance') }}</th>
                                                                <th class="text-right">{{ __('Fiber') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($ep['points'] as $pt)
                                                                <tr>
                                                                    <td class="text-gray-900 font-mono text-xs">{{ $pt['node_code'] }}</td>
                                                                    <td class="text-gray-500">{{ $pt['box_type'] }}</td>
                                                                    <td class="text-gray-500 font-mono text-xs">{{ $pt['zp_code'] }}</td>
                                                                    <td class="text-right text-gray-500">{{ number_format($pt['distance_m'], 1) }} m</td>
                                                                    <td class="text-right text-red-600 font-medium">{{ $pt['fibre_utile_epissuree'] }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Optical Boxes --}}
                    @if (!empty($detailed['equipment']['optical_boxes']['total']))
                        <div class="ff-accordion">
                            <button @click="openSections.includes('boxes') ? openSections = openSections.filter(s => s !== 'boxes') : openSections.push('boxes')"
                                class="ff-accordion-trigger">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <span class="ff-section-header">{{ __('Optical Boxes') }}</span>
                                    <span class="ff-badge-neutral">{{ $detailed['equipment']['optical_boxes']['total'] }} · {{ $detailed['equipment']['optical_boxes']['total_cassettes'] }} {{ __('cassettes') }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openSections.includes('boxes') }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openSections.includes('boxes')" x-collapse x-cloak>
                                <div class="ff-accordion-content">
                                    @if (!empty($detailed['equipment']['optical_boxes']['by_reference']))
                                        <div class="overflow-x-auto">
                                            <table class="ff-table text-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">{{ __('Designation') }}</th>
                                                        <th class="text-left">{{ __('Manufacturer') }}</th>
                                                        <th class="text-center">{{ __('Type') }}</th>
                                                        <th class="text-center">{{ __('Status') }}</th>
                                                        <th class="text-right">{{ __('Count') }}</th>
                                                        <th class="text-right">{{ __('Cassettes') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach (collect($detailed['equipment']['optical_boxes']['by_reference'])->sortByDesc('count') as $ref)
                                                        <tr>
                                                            <td class="max-w-xs">
                                                                <div class="truncate font-medium text-sm text-gray-900">{{ $ref['designation'] }}</div>
                                                                <div class="text-xs text-gray-400 font-mono">{{ $ref['rf_code'] }}</div>
                                                            </td>
                                                            <td class="text-gray-700">{{ $ref['manufacturer'] }}</td>
                                                            <td class="text-center text-gray-500">{{ $ref['logical_type'] ?? '-' }}</td>
                                                            <td class="text-center text-gray-500">{{ $ref['statut'] ?? '-' }}</td>
                                                            <td class="text-right text-gray-500">{{ $ref['count'] }}</td>
                                                            <td class="text-right text-gray-500">{{ $ref['cassettes'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Supports & Conduits --}}
                    @if (!empty($detailed['supports']['technical_points']['total']) || !empty($detailed['supports']['conduits']['by_statut']))
                        <div class="ff-accordion">
                            <button @click="openSections.includes('supports') ? openSections = openSections.filter(s => s !== 'supports') : openSections.push('supports')"
                                class="ff-accordion-trigger">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span class="ff-section-header">{{ __('Supports & Conduits') }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openSections.includes('supports') }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openSections.includes('supports')" x-collapse x-cloak>
                                <div class="ff-accordion-content">
                                    @if (!empty($detailed['supports']['technical_points']['total']))
                                        @php
                                            $typephyLabels = ['A' => 'Appui', 'C' => 'Chambre', 'F' => 'Façade', 'I' => 'Immeuble', 'Z' => 'Autre'];
                                            $typeCols = ['A', 'C', 'F', 'I', 'Z'];
                                            $ownerNames = $detailed['supports']['organismes'] ?? [];
                                            $grandTypes = array_fill_keys($typeCols, 0);
                                            foreach ($detailed['supports']['technical_points']['by_statut'] as $sg) {
                                                foreach ($sg['by_owner'] as $od) {
                                                    foreach ($typeCols as $tc) {
                                                        $grandTypes[$tc] += $od[$tc] ?? 0;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <div class="grid grid-cols-5 gap-3 text-sm mb-4">
                                            @foreach ($typeCols as $tc)
                                                <div class="ff-card p-3 text-center">
                                                    <div class="text-lg font-semibold">{{ $grandTypes[$tc] }}</div>
                                                    <div class="text-xs text-gray-500">{{ $typephyLabels[$tc] }}</div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @foreach ($detailed['supports']['technical_points']['by_statut'] as $statut => $group)
                                            @php
                                                $owners = array_keys($group['by_owner']);
                                                sort($owners);
                                            @endphp
                                            <div class="overflow-x-auto mb-4">
                                                <table class="ff-table text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-left">{{ __('Owner') }}</th>
                                                            @foreach ($typeCols as $tc)
                                                                <th class="text-right">{{ $typephyLabels[$tc] }}</th>
                                                            @endforeach
                                                            <th class="text-right">{{ __('Total') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $colTotals = array_fill_keys($typeCols, 0); @endphp
                                                        @foreach ($owners as $owner)
                                                            @php
                                                                $types = $group['by_owner'][$owner];
                                                                $rowTotal = array_sum($types);
                                                                $ownerLabel = $ownerNames[$owner] ?? $owner;
                                                            @endphp
                                                            <tr>
                                                                <td class="text-gray-900 font-medium">{{ $ownerLabel }}</td>
                                                                @foreach ($typeCols as $tc)
                                                                    @php $val = $types[$tc] ?? 0; $colTotals[$tc] += $val; @endphp
                                                                    <td class="text-right text-gray-500">{{ $val ?: '-' }}</td>
                                                                @endforeach
                                                                <td class="text-right text-gray-900 font-semibold">{{ $rowTotal }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="text-xs font-semibold text-gray-700">
                                                        <tr class="bg-surface-50">
                                                            <td class="px-3 py-2">{{ __('Total') }}</td>
                                                            @foreach ($typeCols as $tc)
                                                                <td class="px-3 py-2 text-right">{{ $colTotals[$tc] }}</td>
                                                            @endforeach
                                                            <td class="px-3 py-2 text-right">{{ $group['count'] }}</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if (!empty($detailed['supports']['conduits']['by_statut']))
                                        @php
                                            $grandUg = 0; $grandAe = 0; $grandFo = 0;
                                            foreach ($detailed['supports']['conduits']['by_statut'] as $sg) {
                                                foreach ($sg['by_owner'] as $od) {
                                                    $grandUg += $od['underground_length'] ?? 0;
                                                    $grandAe += $od['aerial_length'] ?? 0;
                                                    $grandFo += $od['facade_other_length'] ?? 0;
                                                }
                                            }
                                        @endphp
                                        <h4 class="ff-section-header text-sm mt-6 mb-3">{{ __('Conduits') }}</h4>
                                        <div class="grid grid-cols-3 gap-3 text-sm mb-4">
                                            <div class="ff-card p-3">
                                                <div class="text-lg font-semibold text-emerald-600">{{ number_format($grandUg, 1) }} m</div>
                                                <div class="text-xs text-emerald-600">{{ __('Underground') }}</div>
                                            </div>
                                            <div class="ff-card p-3">
                                                <div class="text-lg font-semibold text-sky-600">{{ number_format($grandAe, 1) }} m</div>
                                                <div class="text-xs text-sky-600">{{ __('Aerial') }}</div>
                                            </div>
                                            <div class="ff-card p-3">
                                                <div class="text-lg font-semibold text-amber-600">{{ number_format($grandFo, 1) }} m</div>
                                                <div class="text-xs text-amber-600">{{ __('Facade/Other') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Logements --}}
                    @if (isset($detailed['logements']))
                        <div class="ff-accordion">
                            <button @click="openSections.includes('logements') ? openSections = openSections.filter(s => s !== 'logements') : openSections.push('logements')"
                                class="ff-accordion-trigger">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    <span class="ff-section-header">{{ __('Housing & Addresses') }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openSections.includes('logements') }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openSections.includes('logements')" x-collapse x-cloak>
                                <div class="ff-accordion-content">
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                                        <div class="ff-card p-3">
                                            <div class="text-lg font-semibold text-purple-600">{{ $detailed['logements']['logements']['total'] }}</div>
                                            <div class="text-xs text-purple-600">{{ __('Total') }}</div>
                                        </div>
                                        <div class="ff-card p-3">
                                            <div class="text-lg font-semibold text-brand-600">{{ $detailed['logements']['connected'] }}</div>
                                            <div class="text-xs text-brand-600">{{ __('Connected') }}</div>
                                        </div>
                                        <div class="ff-card p-3">
                                            <div class="text-lg font-semibold text-gray-900">{{ $detailed['logements']['logements']['occupation_rate'] }}%</div>
                                            <div class="text-xs text-gray-500">{{ __('Occupation') }}</div>
                                        </div>
                                    </div>

                                    @if (!empty($detailed['addresses']))
                                        <h4 class="ff-section-header text-sm mb-3">{{ __('Addresses') }}</h4>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                            <div class="ff-card p-3">
                                                <div class="text-lg font-semibold text-purple-600">{{ $detailed['addresses']['prises_habitation'] ?? 0 }}</div>
                                                <div class="text-xs text-purple-600">{{ __('Residential') }}</div>
                                            </div>
                                            <div class="ff-card p-3">
                                                <div class="text-lg font-semibold text-brand-600">{{ $detailed['addresses']['prises_professionnelles'] ?? 0 }}</div>
                                                <div class="text-xs text-brand-600">{{ __('Professional') }}</div>
                                            </div>
                                            <div class="ff-card p-3">
                                                <div class="text-lg font-semibold text-brand-600">{{ $detailed['addresses']['locaux_habitation'] ?? 0 }}</div>
                                                <div class="text-xs text-brand-600">{{ __('Housing') }}</div>
                                            </div>
                                            <div class="ff-card p-3">
                                                <div class="text-lg font-semibold text-amber-600">{{ $detailed['addresses']['immeubles_neufs'] ?? 0 }}</div>
                                                <div class="text-xs text-amber-600">{{ __('New Buildings') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            @endif

            {{-- Chat --}}
            <div class="ff-card"
                 x-data="auditChat({{ $audit->id }}, {{ $project->id }})"
                 x-init="init">
                <div class="p-6">
                    <h3 class="ff-section-header flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        {{ __('FTTH Assistant') }}
                    </h3>

                    <div class="border border-surface-200 rounded-lg overflow-hidden">
                        <div class="h-80 overflow-y-auto p-4 space-y-4 bg-surface-50" x-ref="messagesContainer">
                            <template x-for="(msg, index) in messages" :key="index">
                                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                                    <div :class="msg.role === 'user'
                                        ? 'bg-brand-600 text-white rounded-lg rounded-br-sm px-4 py-2 max-w-[80%]'
                                        : 'bg-white border border-surface-200 rounded-lg rounded-bl-sm px-4 py-2 max-w-[80%]'">
                                        <p class="text-sm whitespace-pre-wrap" x-text="msg.content"></p>
                                    </div>
                                </div>
                            </template>
                            <div x-show="loading" class="flex justify-start">
                                <div class="bg-white border border-surface-200 rounded-lg rounded-bl-sm px-4 py-3">
                                    <div class="flex gap-1">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                    </div>
                                </div>
                            </div>
                            <div x-show="!loading && messages.length === 0" class="text-center text-gray-400 text-sm py-8">
                                {{ __('Ask a question about this audit...') }}
                            </div>
                        </div>

                        <form @submit.prevent="sendMessage" class="border-t border-surface-200 p-3 bg-white flex gap-2">
                            <input type="text" x-model="message"
                                class="ff-input flex-1"
                                :placeholder="loading ? '{{ __('Thinking...') }}' : '{{ __('Ask a question...') }}'"
                                :disabled="loading" autocomplete="off">
                            <button type="submit" :disabled="!message.trim() || loading"
                                class="ff-btn-primary px-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 0l-7 7m7-7l7 7"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Back --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.projects.audits.index', $project) }}" class="ff-btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back to Audits') }}
                </a>
                <a href="{{ route('admin.projects.show', $project) }}" class="ff-btn-ghost">
                    {{ __('Project') }}
                </a>
            </div>

        </div>
    </div>
    @else
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="ff-empty">
                    <div class="ff-empty-icon">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-gray-500">{{ __('This audit is still processing or has failed.') }}</p>
                    <a href="{{ route('admin.projects.audits.index', $project) }}" class="ff-btn-secondary mt-4">
                        {{ __('Back to Audits') }}
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
