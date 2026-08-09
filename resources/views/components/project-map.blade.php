@props(['project', 'initialCenter' => [46.603354, 1.888334], 'initialZoom' => 8, 'height' => '500px'])

<div
    x-data="projectMap({{ $project->id }}, {{ json_encode($initialCenter) }}, {{ $initialZoom }})"
    class="relative"
>
    <div class="relative rounded-lg overflow-hidden border border-surface-200">
        <div
            id="map-{{ $project->id }}"
            class="w-full"
            style="height: {{ $height }};"
            x-ref="mapContainer"
        ></div>

        {{-- Floating toolbar --}}
        <div class="absolute top-3 left-3 right-3 sm:right-auto z-[1000] flex flex-wrap items-center gap-2 bg-white/95 backdrop-blur border border-surface-100 rounded-xl shadow-lg shadow-slate-900/5 p-2">
            <select
                id="layer-select"
                x-model="selectedLayer"
                x-on:change="filterFeatures()"
                class="ff-input !w-auto !py-1.5 !text-sm"
                aria-label="{{ __('Layer') }}"
            >
                <option value="">{{ __('All layers') }}</option>
                <option value="t_noeud">{{ __('Nodes') }}</option>
                <option value="t_ptech">{{ __('Equipment') }}</option>
                <option value="t_cable">{{ __('Cables') }}</option>
                <option value="t_cableline">{{ __('Cable Lines') }}</option>
                <option value="t_cheminement">{{ __('Paths') }}</option>
                <option value="t_conduite">{{ __('Conduits') }}</option>
                <option value="t_znro">{{ __('NRO Zones') }}</option>
                <option value="t_zsro">{{ __('SRO Zones') }}</option>
                <option value="t_zpbo">{{ __('PBO Zones') }}</option>
            </select>

            <input
                id="search-input"
                type="text"
                x-model="searchQuery"
                x-on:input.debounce="filterFeatures()"
                placeholder="{{ __('Search by code, name...') }}"
                class="ff-input !w-auto !py-1.5 !text-sm"
                aria-label="{{ __('Search') }}"
            />

            <span class="text-xs text-gray-500 px-1" x-text="featureCount > 0 ? featureCount + ' {{ __('features') }}' : ''"></span>
        </div>

        {{-- Legend --}}
        <div class="absolute bottom-3 right-3 z-[1000]" x-data="{ legendOpen: true }">
            <div x-show="legendOpen" x-collapse x-cloak
                class="bg-white/95 backdrop-blur border border-surface-100 rounded-xl shadow-lg shadow-slate-900/5 p-3 text-xs mb-2">
                <p class="font-semibold text-gray-700 mb-2">{{ __('Legend') }}</p>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#6366f1"></span>
                        {{ __('Transport') }}
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#10b981"></span>
                        {{ __('Distribution') }}
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#f59e0b"></span>
                        {{ __('NRO Zones') }}
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#ef4444"></span>
                        {{ __('SRO Zones') }}
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#8b5cf6"></span>
                        {{ __('PBO Zones') }}
                    </div>
                </div>
            </div>
            <button @click="legendOpen = !legendOpen" :aria-expanded="legendOpen"
                class="ml-auto flex items-center gap-1.5 bg-white/95 backdrop-blur border border-surface-100 rounded-lg shadow-lg shadow-slate-900/5 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:text-brand-600 hover:border-brand-200 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 3.5 4 8 4s8-2 8-4V7M4 7c0 2 3.5 4 8 4s8-2 8-4M4 7c0-2 3.5-4 8-4s8 2 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                <span x-text="legendOpen ? '{{ __('Hide legend') }}' : '{{ __('Legend') }}'"></span>
            </button>
        </div>

        {{-- Loading overlay --}}
        <div x-show="loading" class="absolute inset-0 z-[1100] flex items-center justify-center bg-white/60">
            <svg class="animate-spin h-8 w-8 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        </div>
    </div>
</div>
