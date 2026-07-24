@props(['project', 'initialCenter' => [46.603354, 1.888334], 'initialZoom' => 8])

<div
    x-data="projectMap({{ $project->id }}, {{ json_encode($initialCenter) }}, {{ $initialZoom }})"
    class="relative"
>
    <div class="mb-4 flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <label for="layer-select" class="text-sm font-medium text-gray-700">{{ __('Layer') }}:</label>
            <select
                id="layer-select"
                x-model="selectedLayer"
                x-on:change="filterFeatures()"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
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
        </div>

        <div class="flex items-center gap-2">
            <label for="search-input" class="text-sm font-medium text-gray-700">{{ __('Search') }}:</label>
            <input
                id="search-input"
                type="text"
                x-model="searchQuery"
                x-on:input.debounce="filterFeatures()"
                placeholder="{{ __('Search by code, name...') }}"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
            />
        </div>

        <div class="text-sm text-gray-500" x-text="featureCount > 0 ? featureCount + ' {{ __('features') }}' : ''"></div>
    </div>

    <div
        id="map-{{ $project->id }}"
        class="w-full rounded-lg border border-gray-300"
        style="height: 500px;"
        x-ref="mapContainer"
    ></div>

    <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/60 rounded-lg">
        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    </div>


</div>


