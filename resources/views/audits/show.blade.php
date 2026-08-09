<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :title="__('Audit #') . $audit->id"
            :breadcrumbs="[
                ['label' => $project->name, 'url' => route('admin.projects.show', $project)],
                ['label' => __('Audits'), 'url' => route('admin.projects.audits.index', $project)],
            ]"
        >
            <x-slot name="meta">
                <x-status-badge :status="$audit->status->value">{{ ucfirst($audit->status->value) }}</x-status-badge>
                <span class="ff-pill">{{ $audit->project_type_at_audit }}</span>
                <span class="ff-pill">{{ $audit->phase_at_audit }}</span>
                <span class="ff-pill">{{ __('by') }} {{ $audit->performer?->name ?? $audit->performed_by }}</span>
                @if ($audit->completed_at)
                    <span class="ff-pill">{{ $audit->completed_at->format('M j, Y g:i A') }}</span>
                @endif
            </x-slot>
            <x-slot name="actions">
                @if ($audit->status->value === 'completed')
                    <a href="{{ route('admin.projects.audits.pdf', [$project, $audit]) }}" class="ff-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('PDF') }}
                    </a>
                    <a href="{{ route('admin.projects.audits.excel', [$project, $audit]) }}" class="ff-btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('Excel') }}
                    </a>
                @endif
                @if ($audit->isRetryable())
                    <form method="POST" action="{{ route('admin.projects.audits.retry', [$project, $audit]) }}" class="inline">
                        @csrf
                        <button type="submit" class="ff-btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            {{ __('Retry') }}
                        </button>
                    </form>
                @endif
            </x-slot>
        </x-page-header>
    </x-slot>

    @php
        $score = round($audit->quality_score, 1);
        $ns = $audit->network_statistics;
        $detailed = $ns['detailed'] ?? null;
        $aiData = is_array($audit->ai_summary) ? $audit->ai_summary : null;
        $layers = collect($ns)->except(['total_fibers', 'used_fibers', 'spare_fibers', 'occupation_rate', 'detailed']);
        $fpb = $detailed['fibers_per_pbo'] ?? [];
        $hasAnomalies = ! empty($detailed['anomalies'] ?? []);
        $hasCables = ! empty($detailed['cables']['total_count']);
        $hasFibers = ! empty($detailed['fibers']) || ! empty($fpb['feeder_cables']);
        $hasOps = ! empty($fpb['operations_chantier']['epissurages']);
        $hasBoxes = ! empty($detailed['equipment']['optical_boxes']['total']);
        $hasSupports = ! empty($detailed['supports']['technical_points']['total']) || ! empty($detailed['supports']['conduits']['by_statut']);
        $hasLogements = isset($detailed['logements']);
    @endphp

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <x-alert type="success">{{ session('success') }}</x-alert>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <x-alert type="error">{{ session('error') }}</x-alert>
        </div>
    @endif

    @if ($audit->error_message)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <x-alert type="error">{{ $audit->error_message }}</x-alert>
        </div>
    @endif

    @if ($audit->status->value === 'completed')
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                {{-- Sticky section nav --}}
                <div class="sticky top-14 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 bg-white/90 backdrop-blur border-b border-surface-100">
                    <nav class="flex gap-1 overflow-x-auto py-2" aria-label="{{ __('Sections') }}">
                        <a href="#section-score" class="ff-tab-link">{{ __('Score') }}</a>
                        @if ($aiData || $audit->ai_summary)
                            <a href="#section-ai" class="ff-tab-link">{{ __('AI Analysis') }}</a>
                        @endif
                        @if ($hasAnomalies)
                            <a href="#section-anomalies" class="ff-tab-link">{{ __('Anomalies') }}</a>
                        @endif
                        @if ($layers->isNotEmpty())
                            <a href="#section-layers" class="ff-tab-link">{{ __('Layers') }}</a>
                        @endif
                        @if ($hasCables)
                            <a href="#section-cables" class="ff-tab-link">{{ __('Cables') }}</a>
                        @endif
                        @if ($hasFibers)
                            <a href="#section-fibers" class="ff-tab-link">{{ __('Fiber') }}</a>
                        @endif
                        @if ($hasOps)
                            <a href="#section-ops" class="ff-tab-link">{{ __('Operations') }}</a>
                        @endif
                        @if ($hasBoxes)
                            <a href="#section-boxes" class="ff-tab-link">{{ __('Boxes') }}</a>
                        @endif
                        @if ($hasSupports)
                            <a href="#section-supports" class="ff-tab-link">{{ __('Supports') }}</a>
                        @endif
                        @if ($hasLogements)
                            <a href="#section-logements" class="ff-tab-link">{{ __('Housing') }}</a>
                        @endif
                        <a href="#section-assistant" class="ff-tab-link">{{ __('Assistant') }}</a>
                    </nav>
                </div>

                {{-- Score Hero --}}
                <div id="section-score" class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <div class="lg:col-span-2 ff-card">
                        <div class="p-6 flex flex-col items-center justify-center">
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
                    $cableCount = $detailed['cables']['total_count'] ?? 0;
                    $cableLength = $detailed['cables']['total_length_m'] ?? 0;
                    $boxCount = $detailed['equipment']['optical_boxes']['total'] ?? 0;
                    $totalCapa = array_sum(array_column($fpb['feeder_cables'] ?? [], 'capacity'));
                    $totalUtile = $fpb['total_fiber_utile'] ?? 0;
                    $occRate = $totalCapa > 0 ? round(($totalUtile / $totalCapa) * 100) : 0;
                    $pboCount = $fpb['pbo_count'] ?? 0;
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="ff-stat-card cursor-pointer hover:border-brand-300 transition-colors" onclick="document.getElementById('section-anomalies').scrollIntoView({behavior: 'smooth'})">
                        <div class="ff-stat-card-icon bg-warning-50">
                            <svg class="w-5 h-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="ff-stat-card-value mt-3">{{ number_format($anomalyCount) }}</div>
                        <div class="ff-stat-card-label">{{ __('Anomalies') }} · <span class="text-red-600">{{ $criticalCount }} {{ __('critical') }}</span></div>
                    </div>

                    <div class="ff-stat-card">
                        <div class="ff-stat-card-icon bg-info-50">
                            <svg class="w-5 h-5 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 3.5 4 8 4s8-2 8-4V7M4 7c0 2 3.5 4 8 4s8-2 8-4M4 7c0-2 3.5-4 8-4s8 2 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                        </div>
                        <div class="ff-stat-card-value mt-3">{{ $layers->count() }}</div>
                        <div class="ff-stat-card-label">{{ __('Layers') }}</div>
                    </div>

                    <div class="ff-stat-card">
                        <div class="ff-stat-card-icon bg-success-50">
                            <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
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
                @include('audits.partials.ai-analysis')

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
                                    ->sortBy(fn ($a) => $severityOrder[$a['severity']] ?? 99);
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

                {{-- Accordion sections --}}
                @include('audits.partials.network-sections')

                {{-- Assistant --}}
                @include('audits.partials.assistant')

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
                <div x-data="auditWatcher('{{ route('admin.projects.audits.status', [$project, $audit]) }}')">
                    <div class="ff-card">
                        @if (in_array($audit->status->value, ['pending', 'running']))
                            <div class="ff-empty py-16">
                                <svg class="w-10 h-10 text-brand-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <p class="text-gray-500 mt-4">{{ __('This audit is processing. This page will refresh automatically when it completes.') }}</p>
                                <a href="{{ route('admin.projects.audits.index', $project) }}" class="ff-btn-secondary mt-4">
                                    {{ __('Back to Audits') }}
                                </a>
                            </div>
                        @else
                            <div class="ff-empty py-16">
                                <svg class="w-10 h-10 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-gray-500 mt-4">{{ __('This audit could not be completed.') }}</p>
                                <a href="{{ route('admin.projects.audits.index', $project) }}" class="ff-btn-secondary mt-4">
                                    {{ __('Back to Audits') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
