<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.projects.audits.index', $project) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Audit') }} #{{ $audit->id }} — {{ $project->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                            <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($audit->status->value)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('running') bg-blue-100 text-blue-800 @break
                                        @case('completed') bg-green-100 text-green-800 @break
                                        @case('failed') bg-red-100 text-red-800 @break
                                    @endswitch
                                ">
                                    {{ ucfirst($audit->status->value) }}
                                </span>
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Project Type') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $audit->project_type_at_audit }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Study Phase') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $audit->phase_at_audit }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Performer') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $audit->performer?->name ?? $audit->performed_by }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Started') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $audit->started_at?->format('M j, Y g:i A') ?? __('N/A') }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Completed') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $audit->completed_at?->format('M j, Y g:i A') ?? __('N/A') }}</dd>
                        </div>
                        @if ($audit->error_message)
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-red-500">{{ __('Error') }}</dt>
                                <dd class="mt-1 text-sm text-red-700 sm:mt-0 sm:col-span-2">{{ $audit->error_message }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if ($audit->status->value === 'completed')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Quality Scores') }}</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div class="p-4 rounded-lg
                                @if ($audit->quality_score >= 90) bg-green-50 border border-green-200
                                @elseif ($audit->quality_score >= 75) bg-blue-50 border border-blue-200
                                @elseif ($audit->quality_score >= 50) bg-yellow-50 border border-yellow-200
                                @else bg-red-50 border border-red-200 @endif
                            ">
                                <div class="text-2xl font-bold
                                    @if ($audit->quality_score >= 90) text-green-700
                                    @elseif ($audit->quality_score >= 75) text-blue-700
                                    @elseif ($audit->quality_score >= 50) text-yellow-700
                                    @else text-red-700 @endif
                                ">{{ number_format($audit->quality_score, 1) }}</div>
                                <div class="text-sm text-gray-600">{{ __('Overall') }}</div>
                                <div class="text-xs text-gray-500">
                                    @php
                                        $label = match (true) {
                                            $audit->quality_score >= 90 => 'Excellent',
                                            $audit->quality_score >= 75 => 'Good',
                                            $audit->quality_score >= 50 => 'Acceptable',
                                            default => 'Non-compliant',
                                        };
                                    @endphp
                                    {{ __($label) }}
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="text-lg font-semibold text-gray-800">{{ number_format($audit->connectivity_score, 1) }}</div>
                                <div class="text-sm text-gray-600">{{ __('Connectivity') }}</div>
                                <div class="text-xs text-gray-400">{{ __('Weight: 40%') }}</div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="text-lg font-semibold text-gray-800">{{ number_format($audit->coherence_score, 1) }}</div>
                                <div class="text-sm text-gray-600">{{ __('Coherence') }}</div>
                                <div class="text-xs text-gray-400">{{ __('Weight: 30%') }}</div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="text-lg font-semibold text-gray-800">{{ number_format($audit->capacity_score, 1) }}</div>
                                <div class="text-sm text-gray-600">{{ __('Capacity') }}</div>
                                <div class="text-xs text-gray-400">{{ __('Weight: 20%') }}</div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="text-lg font-semibold text-gray-800">{{ number_format($audit->extensibility_score, 1) }}</div>
                                <div class="text-sm text-gray-600">{{ __('Extensibility') }}</div>
                                <div class="text-xs text-gray-400">{{ __('Weight: 10%') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @php $aiData = is_array($audit->ai_summary) ? $audit->ai_summary : null; @endphp
                    @if ($aiData)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Analyse IA') }}</h3>

                                <div class="mb-4">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-1">{{ __('Résumé') }}</h4>
                                    <p class="text-sm text-gray-700">{{ $aiData['summary'] }}</p>
                                </div>

                                <div class="mb-4">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-1">{{ __('Qualité') }}</h4>
                                    <p class="text-sm text-gray-700">{{ $aiData['quality'] }}</p>
                                </div>

                                @if (! empty($aiData['observations']))
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-gray-800 mb-1">{{ __('Observations') }}</h4>
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($aiData['observations'] as $obs)
                                                <li class="text-sm text-gray-700">{{ $obs }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (! empty($aiData['risks']))
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-red-700 mb-1">{{ __('Risques') }}</h4>
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($aiData['risks'] as $risk)
                                                <li class="text-sm text-red-600">{{ $risk }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (! empty($aiData['recommendations']))
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-blue-700 mb-1">{{ __('Recommandations') }}</h4>
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($aiData['recommendations'] as $rec)
                                                <li class="text-sm text-blue-600">{{ $rec }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($audit->model_used)
                                    <p class="mt-4 text-xs text-gray-400">{{ __('Modèle') }}: {{ $audit->model_used }} @if ($audit->tokens_used) | {{ __('Jetons') }}: {{ $audit->tokens_used }} @endif</p>
                                @endif
                            </div>
                        </div>
                    @elseif ($audit->ai_summary)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('AI Summary') }}</h3>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $audit->ai_summary }}</p>
                                @if ($audit->model_used)
                                    <p class="mt-4 text-xs text-gray-400">{{ __('Model') }}: {{ $audit->model_used }} @if ($audit->tokens_used) | {{ __('Tokens') }}: {{ $audit->tokens_used }} @endif</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Anomalies') }}</h3>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="px-3 py-2 bg-yellow-50 border border-yellow-200 rounded-md">
                                    <div class="text-lg font-semibold text-yellow-700">{{ $audit->anomaly_count }}</div>
                                    <div class="text-xs text-yellow-600">{{ __('Warnings') }}</div>
                                </div>
                                <div class="px-3 py-2 bg-red-50 border border-red-200 rounded-md">
                                    <div class="text-lg font-semibold text-red-700">{{ $audit->critical_anomaly_count }}</div>
                                    <div class="text-xs text-red-600">{{ __('Critical') }}</div>
                                </div>
                            </div>

                            @if ($audit->network_statistics)
                                @php
                                    $severityOrder = ['critical' => 0, 'warning' => 1, 'info' => 2];
                                    $anomalies = collect($audit->network_statistics['detailed']['anomalies'] ?? [])
                                        ->sortBy(fn($a) => $severityOrder[$a['severity']] ?? 99);
                                    $typeLabels = [
                                        'transport' => 'Transport',
                                        'distribution' => 'Distribution',
                                        'cable' => 'Câble',
                                        'ebp' => 'Boîte (EBP)',
                                        'fiber_saturation' => 'Saturation fibre',
                                        'fiber_no_feeder' => 'Zone non desservie',
                                    ];
                                @endphp

                                @if ($anomalies->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('SHP') }}</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Type d\'erreur') }}</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Message') }}</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Solution possible') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach ($anomalies as $a)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-2 text-gray-700 font-mono text-xs">{{ $a['shp'] ?? '-' }}</td>
                                                    <td class="px-3 py-2">
                                                        <span class="inline-flex items-center gap-1">
                                                            @if ($a['severity'] === 'critical')
                                                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                            @elseif ($a['severity'] === 'warning')
                                                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                                            @else
                                                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                                            @endif
                                                            {{ $typeLabels[$a['type']] ?? ucfirst($a['type']) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2 text-gray-600">{{ $a['message'] }}</td>
                                                    <td class="px-3 py-2 text-gray-600">{{ $a['solution'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif
                        </div>
                    </div>
                </div>

                @if ($audit->network_statistics)
                    @php $detailed = $audit->network_statistics['detailed'] ?? null; @endphp

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Layer Overview') }}</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Layer') }}</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Count') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach (collect($audit->network_statistics)->except(['total_fibers', 'used_fibers', 'spare_fibers', 'occupation_rate', 'detailed']) as $layer => $count)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 text-gray-900">{{ $layer }}</td>
                                                <td class="px-4 py-2 text-right text-gray-500">{{ $count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if ($detailed)
                        @php
                            $typephyLabels = ['A' => 'Appui', 'C' => 'Chambre', 'F' => 'Façade', 'I' => 'Immeuble', 'Z' => 'Autre'];
                        @endphp

                        {{-- Cables --}}
                        @if (!empty($detailed['cables']['total_count']))
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Cables') }}</h3>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                        <div class="p-3 bg-gray-50 rounded-lg border">
                                            <div class="text-lg font-semibold text-gray-800">{{ $detailed['cables']['total_count'] }}</div>
                                            <div class="text-xs text-gray-500">{{ __('Total Cables') }}</div>
                                        </div>
                                        <div class="p-3 bg-gray-50 rounded-lg border">
                                            <div class="text-lg font-semibold text-gray-800">{{ number_format($detailed['cables']['total_length_m'], 1) }} m</div>
                                            <div class="text-xs text-gray-500">{{ __('Total Length') }}</div>
                                        </div>
                                    </div>



                                    @if (!empty($detailed['cables']['by_reference']))
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2 mt-4">{{ __('By Reference') }}</h4>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Designation') }}</th>
                                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Manufacturer') }}</th>
                                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Description') }}</th>
                                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('FO') }}</th>
                                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Mod.') }}</th>
                                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Inst.') }}</th>
                                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Count') }}</th>
                                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Carto (m)') }}</th>
                                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Adj. (m)') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    @foreach (collect($detailed['cables']['by_reference'])->sortByDesc('count') as $ref)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-3 py-2 text-gray-900 max-w-xs">
                                                                <div class="truncate font-medium text-sm" title="{{ $ref['designation'] }}">{{ $ref['designation'] ?: '-' }}</div>
                                                                <div class="text-xs text-gray-400 font-mono">{{ $ref['rf_code'] }}</div>
                                                            </td>
                                                            <td class="px-3 py-2 text-gray-700">{{ $ref['manufacturer'] }}</td>
                                                            <td class="px-3 py-2 text-gray-500 max-w-xs truncate" title="{{ $ref['description'] ?: $ref['designation'] }}">{{ $ref['description'] ?: '-' }}</td>
                                                            <td class="px-3 py-2 text-center text-gray-500">{{ $ref['fiber_count'] ?: '-' }}</td>
                                                            <td class="px-3 py-2 text-center text-gray-500">{{ $ref['modulo'] ?: '-' }}</td>
                                                            <td class="px-3 py-2 text-center text-gray-500">{{ $ref['installation'] ?: '-' }}</td>
                                                            <td class="px-3 py-2 text-right text-gray-500">{{ $ref['count'] }}</td>
                                                            <td class="px-3 py-2 text-right text-gray-500">{{ number_format($ref['carto_length_m'], 1) }}</td>
                                                            <td class="px-3 py-2 text-right text-gray-500">{{ number_format($ref['adjusted_length_m'], 1) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @php
                            $fpb = $detailed['fibers_per_pbo'] ?? [];
                        @endphp

                        {{-- Fibers --}}
                        @if (!empty($detailed['fibers']))
                            @php
                                $feeders = $fpb['feeder_cables'] ?? [];
                                $totalCapa = array_sum(array_column($feeders, 'capacity'));
                                $totalUtile = $fpb['total_fiber_utile'] ?? 0;
                                $totalDispo = $fpb['total_fiber_disponible'] ?? 0;
                                $pboCount = $fpb['pbo_count'] ?? 0;
                                $occRate = $totalCapa > 0 ? round($totalUtile / $totalCapa * 100) : 0;
                            @endphp
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Fiber Usage') }}</h3>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                        <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="text-lg font-semibold text-green-700">{{ $totalCapa }}</div>
                                            <div class="text-xs text-green-600">{{ __('Total Capacity') }}</div>
                                        </div>
                                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <div class="text-lg font-semibold text-blue-700">{{ $totalUtile }}</div>
                                            <div class="text-xs text-blue-600">{{ __('Used') }}</div>
                                        </div>
                                        <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                            <div class="text-lg font-semibold text-yellow-700">{{ $totalDispo }}</div>
                                            <div class="text-xs text-yellow-600">{{ __('Reserve') }}</div>
                                        </div>
                                        <div class="p-3 bg-gray-50 rounded-lg border">
                                            <div class="text-lg font-semibold text-gray-800">{{ $occRate }}%</div>
                                            <div class="text-xs text-gray-500">{{ __('Occupation') }}</div>
                                        </div>
                                    </div>

                                    <p class="text-xs text-gray-500 mb-4">{{ __('Zones PBO') }}: {{ $pboCount }}</p>

                                    @foreach ($feeders as $feeder)
                                            <div class="mt-6 pt-4 border-t border-gray-200">
                                                <h4 class="text-sm font-semibold text-gray-700 mb-2">
                                                    {{ __('Feeder') }}: <span class="font-mono">{{ $feeder['cable_code'] }}</span>
                                                    <span class="text-xs text-gray-400 font-normal">({{ __('Capacity') }}: {{ $feeder['capacity'] }}, {{ __('Utile') }}: {{ $feeder['total_utile'] }}, {{ __('Disponible') }}: {{ $feeder['total_disponible'] }})</span>
                                                </h4>
                                                @if (!empty($feeder['zones']))
                                                    @php
                                                        $pct = $feeder['capacity'] > 0 ? round($feeder['total_utile'] / $feeder['capacity'] * 100) : 0;
                                                        $satClass = $pct >= 100 ? 'bg-red-50 border-red-200' : ($pct >= 75 ? 'bg-yellow-50 border-yellow-200' : '');
                                                    @endphp
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('PBO Zone') }}</th>
                                                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Prises') }}</th>
                                                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Utile') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-200">
                                                                @foreach ($feeder['zones'] as $zone)
                                                                    <tr class="hover:bg-gray-50">
                                                                        <td class="px-3 py-2 text-gray-900 font-mono text-xs">{{ $zone['zp_code'] }}</td>
                                                                        <td class="px-3 py-2 text-right text-gray-500">{{ $zone['prises'] }}</td>
                                                                        <td class="px-3 py-2 text-right text-gray-700 font-medium">{{ $zone['fiber_utile'] }}</td>
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
                        @else
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Fiber Usage') }}</h3>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                        <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="text-lg font-semibold text-green-700">{{ $detailed['fibers']['total_capacity'] }}</div>
                                            <div class="text-xs text-green-600">{{ __('Total Capacity') }}</div>
                                        </div>
                                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <div class="text-lg font-semibold text-blue-700">{{ $detailed['fibers']['total_used'] }}</div>
                                            <div class="text-xs text-blue-600">{{ __('Used') }}</div>
                                        </div>
                                        <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                            <div class="text-lg font-semibold text-yellow-700">{{ $detailed['fibers']['spare_fibers'] }}</div>
                                            <div class="text-xs text-yellow-600">{{ __('Reserve') }}</div>
                                        </div>
                                        <div class="p-3 bg-gray-50 rounded-lg border">
                                            <div class="text-lg font-semibold text-gray-800">{{ $detailed['fibers']['occupation_rate'] }}%</div>
                                            <div class="text-xs text-gray-500">{{ __('Occupation') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Opérations Chantier --}}
                        @if (!empty($fpb['operations_chantier']['epissurages']))
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Opérations Chantier') }}</h3>

                                    @foreach ($fpb['operations_chantier']['epissurages'] as $ep)
                                        <div class="mb-4 pb-4 border-b border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-1">
                                                {{ __('Cable') }}: <span class="font-mono">{{ $ep['cable_code'] }}</span>
                                                <span class="text-xs text-gray-400 font-normal">({{ __('Capacity') }}: {{ $ep['capacity'] }}, {{ __('Length') }}: {{ number_format($ep['length_m'], 1) }} m)</span>
                                            </h4>

                                            @if (!empty($ep['points']))
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Box') }}</th>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Type') }}</th>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('PBO Zone') }}</th>
                                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Distance (m)') }}</th>
                                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Utile épissurée') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            @foreach ($ep['points'] as $pt)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="px-3 py-2 text-gray-900 font-mono text-xs">{{ $pt['node_code'] }}</td>
                                                                    <td class="px-3 py-2 text-gray-500">{{ $pt['box_type'] }}</td>
                                                                    <td class="px-3 py-2 text-gray-500 font-mono text-xs">{{ $pt['zp_code'] }}</td>
                                                                    <td class="px-3 py-2 text-right text-gray-500">{{ number_format($pt['distance_m'], 1) }}</td>
                                                                    <td class="px-3 py-2 text-right text-red-600 font-medium">{{ $pt['fibre_utile_epissuree'] }}</td>
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
                        @endif



                            @if (!empty($detailed['equipment']['optical_boxes']['total']))
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Optical Boxes') }}</h3>
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div class="p-3 bg-gray-50 rounded-lg border">
                                                <div class="text-lg font-semibold text-gray-800">{{ $detailed['equipment']['optical_boxes']['total'] }}</div>
                                                <div class="text-xs text-gray-500">{{ __('Total Boxes') }}</div>
                                            </div>
                                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                <div class="text-lg font-semibold text-blue-700">{{ $detailed['equipment']['optical_boxes']['total_cassettes'] }}</div>
                                                <div class="text-xs text-blue-600">{{ __('Cassettes') }}</div>
                                            </div>
                                        </div>

                                        @if (!empty($detailed['equipment']['optical_boxes']['by_reference']))
                                            <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ __('By Reference') }}</h4>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Designation') }}</th>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Manufacturer') }}</th>
                                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Log. Type') }}</th>
                                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Statut') }}</th>
                                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Avct') }}</th>
                                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Count') }}</th>
                                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Cassettes') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200">
                                                        @foreach (collect($detailed['equipment']['optical_boxes']['by_reference'])->sortByDesc('count') as $ref)
                                                            <tr class="hover:bg-gray-50">
                                                                <td class="px-3 py-2 text-gray-900 max-w-xs">
                                                                    <div class="truncate font-medium text-sm" title="{{ $ref['designation'] }}">{{ $ref['designation'] }}</div>
                                                                    <div class="text-xs text-gray-400 font-mono">{{ $ref['rf_code'] }}</div>
                                                                </td>
                                                                <td class="px-3 py-2 text-gray-700">{{ $ref['manufacturer'] }}</td>
                                                                <td class="px-3 py-2 text-center text-gray-500">{{ $ref['logical_type'] ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-center text-gray-500">{{ $ref['statut'] ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-center text-gray-500">{{ $ref['avancement'] ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-right text-gray-500">{{ $ref['count'] }}</td>
                                                                <td class="px-3 py-2 text-right text-gray-500">{{ $ref['cassettes'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                        {{-- Supports --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @php $ownerNames = $detailed['supports']['organismes'] ?? []; @endphp
                            @if (!empty($detailed['supports']['technical_points']['total']))
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Supports') }}</h3>
                                        <div class="p-3 bg-gray-50 rounded-lg border mb-4 inline-block">
                                            <div class="text-lg font-semibold text-gray-800">{{ $detailed['supports']['technical_points']['total'] }}</div>
                                            <div class="text-xs text-gray-500">{{ __('Total Supports') }}</div>
                                        </div>

                                        @php
                                            $typeCols = ['A', 'C', 'F', 'I', 'Z'];
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
                                            <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                                <div class="text-lg font-semibold text-green-700">{{ $grandTypes['A'] }}</div>
                                                <div class="text-xs text-green-600">{{ $typephyLabels['A'] }}</div>
                                            </div>
                                            <div class="p-3 bg-sky-50 rounded-lg border border-sky-200">
                                                <div class="text-lg font-semibold text-sky-700">{{ $grandTypes['C'] }}</div>
                                                <div class="text-xs text-sky-600">{{ $typephyLabels['C'] }}</div>
                                            </div>
                                            <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                                <div class="text-lg font-semibold text-purple-700">{{ $grandTypes['F'] }}</div>
                                                <div class="text-xs text-purple-600">{{ $typephyLabels['F'] }}</div>
                                            </div>
                                            <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                                                <div class="text-lg font-semibold text-amber-700">{{ $grandTypes['I'] }}</div>
                                                <div class="text-xs text-amber-600">{{ $typephyLabels['I'] }}</div>
                                            </div>
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                <div class="text-lg font-semibold text-gray-700">{{ $grandTypes['Z'] }}</div>
                                                <div class="text-xs text-gray-600">{{ $typephyLabels['Z'] }}</div>
                                            </div>
                                        </div>
                                        @foreach ($detailed['supports']['technical_points']['by_statut'] as $statut => $group)
                                            @php
                                                $owners = array_keys($group['by_owner']);
                                                sort($owners);
                                            @endphp
                                            <div class="overflow-x-auto mb-4">
                                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Owner') }}</th>
                                                            @foreach ($typeCols as $tc)
                                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ $typephyLabels[$tc] ?? $tc }}</th>
                                                            @endforeach
                                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Total') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200">
                                                        @php $colTotals = array_fill_keys($typeCols, 0); @endphp
                                                        @foreach ($owners as $owner)
                                                            @php
                                                                $types = $group['by_owner'][$owner];
                                                                $rowTotal = array_sum($types);
                                                                $ownerLabel = $ownerNames[$owner] ?? $owner;
                                                            @endphp
                                                            <tr class="hover:bg-gray-50">
                                                                <td class="px-3 py-2 text-gray-900 font-medium" title="{{ $owner }}">{{ $ownerLabel }}</td>
                                                                @foreach ($typeCols as $tc)
                                                                    @php $val = $types[$tc] ?? 0; $colTotals[$tc] += $val; @endphp
                                                                    <td class="px-3 py-2 text-right text-gray-500">{{ $val ?: '-' }}</td>
                                                                @endforeach
                                                                <td class="px-3 py-2 text-right text-gray-800 font-semibold">{{ $rowTotal }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="bg-gray-50 text-xs font-semibold text-gray-700">
                                                        <tr>
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
                                    </div>
                                </div>
                            @endif

                            @if (!empty($detailed['supports']['conduits']['by_statut']))
                                @php $conduitCols = ['underground_length', 'aerial_length']; @endphp
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Conduits') }}</h3>

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
                                        <div class="grid grid-cols-3 gap-3 text-sm mb-4">
                                            <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                                <div class="text-lg font-semibold text-green-700">{{ number_format($grandUg, 1) }} m</div>
                                                <div class="text-xs text-green-600">{{ __('Underground') }}</div>
                                            </div>
                                            <div class="p-3 bg-sky-50 rounded-lg border border-sky-200">
                                                <div class="text-lg font-semibold text-sky-700">{{ number_format($grandAe, 1) }} m</div>
                                                <div class="text-xs text-sky-600">{{ __('Aerial') }}</div>
                                            </div>
                                            <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                                                <div class="text-lg font-semibold text-amber-700">{{ number_format($grandFo, 1) }} m</div>
                                                <div class="text-xs text-amber-600">{{ __('Facade/Other') }}</div>
                                            </div>
                                        </div>

                                        @foreach ($detailed['supports']['conduits']['by_statut'] as $statut => $group)
                                            @php
                                                $owners = array_keys($group['by_owner']);
                                                sort($owners);
                                            @endphp
                                            @if (!empty($owners))
                                                <div class="overflow-x-auto mb-4">
                                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Owner') }}</th>
                                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Underground (m)') }}</th>
                                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Aerial (m)') }}</th>
                                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Facade/Other (m)') }}</th>
                                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Total (m)') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            @php $totUnderground = 0; $totAerial = 0; $totFacadeOther = 0; @endphp
                                                            @foreach ($owners as $owner)
                                                                @php
                                                                    $ug = $group['by_owner'][$owner]['underground_length'] ?? 0;
                                                                    $ae = $group['by_owner'][$owner]['aerial_length'] ?? 0;
                                                                    $fo = $group['by_owner'][$owner]['facade_other_length'] ?? 0;
                                                                    $totUnderground += $ug;
                                                                    $totAerial += $ae;
                                                                    $totFacadeOther += $fo;
                                                                    $ownerLabel = $ownerNames[$owner] ?? $owner;
                                                                @endphp
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="px-3 py-2 text-gray-900 font-medium" title="{{ $owner }}">{{ $ownerLabel }}</td>
                                                                    <td class="px-3 py-2 text-right text-gray-500">{{ number_format($ug, 1) }}</td>
                                                                    <td class="px-3 py-2 text-right text-gray-500">{{ number_format($ae, 1) }}</td>
                                                                    <td class="px-3 py-2 text-right text-gray-500">{{ number_format($fo, 1) }}</td>
                                                                    <td class="px-3 py-2 text-right text-gray-800 font-semibold">{{ number_format($ug + $ae + $fo, 1) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot class="bg-gray-50 text-xs font-semibold text-gray-700">
                                                            <tr>
                                                                <td class="px-3 py-2">{{ __('Total') }}</td>
                                                                <td class="px-3 py-2 text-right">{{ number_format($totUnderground, 1) }}</td>
                                                                <td class="px-3 py-2 text-right">{{ number_format($totAerial, 1) }}</td>
                                                                <td class="px-3 py-2 text-right">{{ number_format($totFacadeOther, 1) }}</td>
                                                                <td class="px-3 py-2 text-right">{{ number_format($totUnderground + $totAerial + $totFacadeOther, 1) }}</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Logements (merged with address stats) --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @if (isset($detailed['logements']))
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Logements') }}</h3>

                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                                            <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                                <div class="text-lg font-semibold text-purple-700">{{ $detailed['logements']['logements']['total'] }}</div>
                                                <div class="text-xs text-purple-600">{{ __('Total') }}</div>
                                            </div>
                                            <div class="p-3 bg-gray-50 rounded-lg border">
                                                <div class="text-lg font-semibold text-gray-800">{{ $detailed['logements']['logements']['max_capacity'] }}</div>
                                                <div class="text-xs text-gray-500">{{ __('Max Capacity') }}</div>
                                            </div>
                                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                <div class="text-lg font-semibold text-blue-700">{{ $detailed['logements']['connected'] }}</div>
                                                <div class="text-xs text-blue-600">{{ __('Connected') }}</div>
                                            </div>
                                            <div class="p-3 bg-gray-50 rounded-lg border">
                                                <div class="text-lg font-semibold text-gray-800">{{ $detailed['logements']['logements']['occupation_rate'] }}%</div>
                                                <div class="text-xs text-gray-500">{{ __('Occupation') }}</div>
                                            </div>
                                            <div class="p-3 bg-gray-50 rounded-lg border">
                                                <div class="text-lg font-semibold text-gray-800">{{ $detailed['logements']['sro_zone_count'] }}</div>
                                                <div class="text-xs text-gray-500">{{ __('SRO Zones') }}</div>
                                            </div>
                                            <div class="p-3 bg-gray-50 rounded-lg border">
                                                <div class="text-lg font-semibold text-gray-800">{{ $detailed['logements']['pbo_zone_count'] }}</div>
                                                <div class="text-xs text-gray-500">{{ __('PBO Zones') }}</div>
                                            </div>
                                        </div>

                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ __('Adresses') }}</h4>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                            <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                                <div class="text-lg font-semibold text-purple-700">{{ $detailed['addresses']['prises_habitation'] ?? 0 }}</div>
                                                <div class="text-xs text-purple-600">{{ __('Prises Hab.') }}</div>
                                            </div>
                                            <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                                                <div class="text-lg font-semibold text-indigo-700">{{ $detailed['addresses']['prises_professionnelles'] ?? 0 }}</div>
                                                <div class="text-xs text-indigo-600">{{ __('Prises Pro.') }}</div>
                                            </div>
                                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                <div class="text-lg font-semibold text-blue-700">{{ $detailed['addresses']['locaux_habitation'] ?? 0 }}</div>
                                                <div class="text-xs text-blue-600">{{ __('Locaux Hab.') }}</div>
                                            </div>
                                            <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                                                <div class="text-lg font-semibold text-amber-700">{{ $detailed['addresses']['immeubles_neufs'] ?? 0 }}</div>
                                                <div class="text-xs text-amber-600">{{ __('Immeubles Neufs') }}</div>
                                            </div>
                                        </div>

                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ __("Par Type d'Immeuble") }}</h4>
                                        <div class="flex flex-wrap gap-3">
                                            @php
                                                $typeim = $detailed['addresses']['by_type_immeuble'] ?? [];
                                            @endphp
                                            @forelse ($typeim as $code => $count)
                                                <div class="p-3 bg-gray-50 rounded-lg border min-w-[120px]">
                                                    <div class="text-lg font-semibold text-gray-800">{{ $count }}</div>
                                                    <div class="text-xs text-gray-500">{{ $code === 'I' ? __('Immeuble') : ($code === 'P' ? __('Pavillon') : $code) }}</div>
                                                </div>
                                            @empty
                                                <div class="text-sm text-gray-400 italic">{{ __('No data') }}</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endif
                @endif
            @endif

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.projects.audits.index', $project) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to Audits') }}
                </a>
                <a href="{{ route('admin.projects.show', $project) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Project') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>