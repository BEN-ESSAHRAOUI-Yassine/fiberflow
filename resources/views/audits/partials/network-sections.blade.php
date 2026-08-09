@if ($ns && $detailed)
    <div class="space-y-4" x-data="{ openSections: ['layers'] }">

        {{-- Layer Overview --}}
        @if ($layers->isNotEmpty())
            <div id="section-layers" class="ff-accordion">
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
        @if (! empty($detailed['cables']['total_count']))
            <div id="section-cables" class="ff-accordion">
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
                        @if (! empty($detailed['cables']['by_reference']))
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
        @if (! empty($detailed['fibers']) || ! empty($fpb['feeder_cables']))
            <div id="section-fibers" class="ff-accordion">
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

                        @if (! empty($fpb['feeder_cables']))
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
                                            @if (! empty($feeder['zones']))
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
        @if (! empty($fpb['operations_chantier']['epissurages']))
            <div id="section-ops" class="ff-accordion">
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
                                @if (! empty($ep['points']))
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
        @if (! empty($detailed['equipment']['optical_boxes']['total']))
            <div id="section-boxes" class="ff-accordion">
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
                        @if (! empty($detailed['equipment']['optical_boxes']['by_reference']))
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
        @if (! empty($detailed['supports']['technical_points']['total']) || ! empty($detailed['supports']['conduits']['by_statut']))
            <div id="section-supports" class="ff-accordion">
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
                        @if (! empty($detailed['supports']['technical_points']['total']))
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

                        @if (! empty($detailed['supports']['conduits']['by_statut']))
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
            <div id="section-logements" class="ff-accordion">
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

                        @if (! empty($detailed['addresses']))
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
