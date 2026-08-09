<x-app-layout>
    @php($personalStatus = $project->personalStatus(auth()->user()))
    <x-slot name="header">
        <x-page-header
            :title="$project->name"
            :breadcrumbs="[['label' => __('Projects'), 'url' => route('admin.projects.index')]]"
        >
            <x-slot name="meta">
                <span class="ff-pill-brand">{{ ucfirst($project->project_type->value) }}</span>
                <span class="ff-pill">{{ $project->study_phase->value }}</span>
                <x-status-badge :status="$personalStatus->value" :title="$personalStatus !== $project->status ? __('You completed an audit on this project') : null">{{ str_replace('_', ' ', $personalStatus->value) }}</x-status-badge>
            </x-slot>
            <x-slot name="actions">
                @unless ($project->trashed())
                    @can('update', $project)
                        <a href="{{ route('admin.projects.edit', $project) }}" class="ff-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            {{ __('Edit') }}
                        </a>
                    @endcan
                @else
                    @can('restore', $project)
                        <form action="{{ route('admin.projects.restore', $project) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="ff-btn-primary">{{ __('Restore') }}</button>
                        </form>
                    @endcan
                @endunless
                <a href="{{ route('admin.projects.index') }}" class="ff-btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back') }}
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    @if ($project->trashed())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <x-alert type="warning">{{ __('This project is archived.') }}</x-alert>
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Map-first hero --}}
            <div class="ff-card !p-0 overflow-hidden">
                <div class="relative">
                    @if ($project->datasets->isNotEmpty())
                        <x-project-map :project="$project" height="480px" />

                        <div class="absolute bottom-4 left-4 right-4 sm:right-auto sm:w-72 z-20 bg-white/95 backdrop-blur border border-surface-100 shadow-lg shadow-slate-900/5 rounded-xl">
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Quick Facts') }}</h3>
                                    <x-status-badge :status="$project->status->value" size="sm">{{ str_replace('_', ' ', $project->status->value) }}</x-status-badge>
                                </div>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex items-center justify-between gap-4">
                                        <dt class="ff-dl-label mb-0">{{ __('Municipality') }}</dt>
                                        <dd class="font-medium text-gray-900 text-right">{{ $project->municipality }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <dt class="ff-dl-label mb-0">{{ __('Client') }}</dt>
                                        <dd class="font-medium text-gray-900 text-right">{{ $project->client }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <dt class="ff-dl-label mb-0">{{ __('Phase') }}</dt>
                                        <dd class="font-medium text-gray-900">{{ $project->study_phase->value }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <dt class="ff-dl-label mb-0">{{ __('Type') }}</dt>
                                        <dd class="font-medium text-gray-900">{{ ucfirst($project->project_type->value) }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <dt class="ff-dl-label mb-0">{{ __('GIS ID') }}</dt>
                                        <dd class="font-mono text-xs text-gray-600">{{ $project->gis_project_id }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <dt class="ff-dl-label mb-0">{{ __('Created') }}</dt>
                                        <dd class="font-medium text-gray-900">{{ $project->created_at->format('M j, Y') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    @else
                        <x-empty-state
                            :title="__('No network data yet')"
                            :description="__('Import a dataset to visualize the fiber network on the map.')">
                            <x-slot name="icon">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </x-slot>
                        </x-empty-state>
                    @endif
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card :label="__('Datasets')" :value="$datasetsCount" iconColor="brand">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    </x-slot>
                </x-stat-card>
                <x-stat-card :label="__('Audits')" :value="$auditsCount" iconColor="success">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-slot>
                </x-stat-card>
                <x-stat-card :label="__('Features')" :value="number_format($featuresCount)" iconColor="purple">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 3.5 4 8 4s8-2 8-4V7M4 7c0 2 3.5 4 8 4s8-2 8-4M4 7c0-2 3.5-4 8-4s8 2 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                    </x-slot>
                </x-stat-card>
                <x-stat-card :label="__('Client')" :value="$project->client" iconColor="sky">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </x-slot>
                </x-stat-card>
            </div>

            {{-- Details: Two Column --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 ff-card">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('Project Details') }}</h3>
                        <dl class="divide-y divide-surface-100">
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('Name') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2">{{ $project->name }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('Description') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2">{{ $project->description ?? __('N/A') }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('GIS Project ID') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2 font-mono text-xs">{{ $project->gis_project_id }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('Created By') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2">{{ $project->creator?->name ?? $project->created_by }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('Created') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2">{{ $project->created_at->format('M j, Y g:i A') }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('Updated') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2">{{ $project->updated_at->format('M j, Y g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="ff-card">
                        <div class="p-6">
                            <h3 class="ff-section-header mb-4">{{ __('Quick Info') }}</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="ff-dl-label">{{ __('Municipality') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $project->municipality }}</dd>
                                </div>
                                @if ($project->parentProject)
                                    <div>
                                        <dt class="ff-dl-label">{{ __('Parent Project') }}</dt>
                                        <dd>
                                            <a href="{{ route('admin.projects.show', $project->parentProject) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">{{ $project->parentProject->name }}</a>
                                        </dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="ff-dl-label">{{ __('Status') }}</dt>
                                    <dd class="mt-1">
                                        <x-status-badge :status="$personalStatus->value" :title="$personalStatus !== $project->status ? __('You completed an audit on this project') : null">{{ str_replace('_', ' ', $personalStatus->value) }}</x-status-badge>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="ff-card">
                        <div class="p-6">
                            <h3 class="ff-section-header mb-3">{{ __('Actions') }}</h3>
                            <div class="space-y-2">
                                <a href="{{ route('admin.projects.audits.index', $project) }}" class="ff-btn-secondary w-full justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    {{ __('View Audits') }}
                                </a>
                                @can('update', $project)
                                    <a href="{{ route('admin.projects.datasets.import', $project) }}" class="ff-btn-secondary w-full justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        {{ __('Import Dataset') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datasets --}}
            @can('update', $project)
                <div class="ff-card">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="ff-section-header">{{ __('Datasets') }}</h3>
                            <span class="ff-badge-neutral">{{ $datasetsCount }} {{ __('total') }}</span>
                        </div>

                        @if ($project->datasets->isEmpty())
                            <x-empty-state
                                :title="__('No datasets imported yet.')"
                                :description="__('Import a GIS dataset to start auditing this project.')">
                            </x-empty-state>
                        @else
                            <div class="overflow-x-auto">
                                <table class="ff-table w-full">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Imported At') }}</th>
                                            <th class="text-right">{{ __('# Features') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($project->datasets as $dataset)
                                            <tr>
                                                <td class="text-gray-900">{{ $dataset->imported_at->format('M j, Y g:i A') }}</td>
                                                <td class="text-right text-gray-500 font-mono text-xs">{{ number_format(collect($dataset->geojson)->flatten()->count()) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endcan

            {{-- Recent Audits --}}
            @if ($project->audits->isNotEmpty())
                <div class="ff-card">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="ff-section-header">{{ __('Recent Audits') }}</h3>
                            <a href="{{ route('admin.projects.audits.index', $project) }}" class="ff-btn-ghost text-sm">
                                {{ __('View All') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                        <div class="space-y-2">
                            @foreach ($project->audits as $audit)
                                <a href="{{ route('admin.projects.audits.show', [$project, $audit]) }}"
                                   class="flex items-center justify-between p-3 rounded-lg hover:bg-surface-50 transition-colors group">
                                    <div class="flex items-center gap-4">
                                        <span class="text-sm font-mono text-gray-400 w-8">#{{ $audit->id }}</span>
                                        <x-status-badge :status="$audit->status->value" :dot="false">{{ ucfirst($audit->status->value) }}</x-status-badge>
                                        @if ($audit->quality_score !== null)
                                            <span class="text-sm font-semibold
                                                @if ($audit->quality_score >= 90) ff-score-excellent
                                                @elseif ($audit->quality_score >= 75) ff-score-good
                                                @elseif ($audit->quality_score >= 50) ff-score-acceptable
                                                @else ff-score-poor @endif
                                            ">{{ number_format($audit->quality_score, 1) }}</span>
                                        @endif
                                        @if ($audit->critical_anomaly_count > 0)
                                            <span class="text-xs text-red-600">{{ $audit->critical_anomaly_count }} {{ __('critical') }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-gray-400">{{ $audit->completed_at?->format('M j') ?? $audit->created_at->format('M j') }}</span>
                                        <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Child Projects --}}
            @if ($project->childProjects->isNotEmpty())
                <div class="ff-card">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('Child Projects') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="ff-table w-full">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Municipality') }}</th>
                                        <th>{{ __('Phase') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($project->childProjects as $child)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.projects.show', $child) }}" class="text-brand-600 hover:text-brand-700 font-medium">{{ $child->name }}</a>
                                            </td>
                                            <td class="text-gray-700">{{ $child->municipality }}</td>
                                            <td class="text-gray-700">{{ $child->study_phase->value }}</td>
                                            <td>
                                                <x-status-badge :status="$child->status->value" :dot="false">{{ str_replace('_', ' ', $child->status->value) }}</x-status-badge>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
