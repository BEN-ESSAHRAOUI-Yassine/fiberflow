<x-app-layout>
    <x-slot name="header">
        <div class="ff-page-header-actions">
            <div>
                <div class="ff-breadcrumb">
                    <a href="{{ route('admin.projects.index') }}">{{ __('Projects') }}</a>
                    <span class="ff-breadcrumb-sep">/</span>
                    <span class="text-gray-900">{{ $project->name }}</span>
                </div>
                <h1 class="ff-page-title text-2xl">{{ $project->name }}</h1>
                <div class="ff-pills mt-2">
                    <span class="ff-pill-brand">{{ ucfirst($project->project_type->value) }}</span>
                    <span class="ff-pill">{{ $project->study_phase->value }}</span>
                    <span class="@switch($project->status->value)
                        @case('draft') ff-pill @break
                        @case('in_progress') ff-pill-warning @break
                        @case('audited') ff-pill-brand @break
                        @case('validated') ff-pill-success @break
                        @case('archived') ff-pill @break
                    @endswitch">
                        {{ str_replace('_', ' ', ucfirst($project->status->value)) }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
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
                @endif
                <a href="{{ route('admin.projects.index') }}" class="ff-btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    @if ($project->trashed())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ __('This project is archived.') }}
            </div>
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="ff-stat-card">
                    <div class="flex items-center justify-between">
                        <div class="ff-stat-card-icon bg-brand-50">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        </div>
                    </div>
                    <div class="ff-stat-card-value mt-3">{{ $datasetsCount }}</div>
                    <div class="ff-stat-card-label">{{ __('Datasets') }}</div>
                </div>

                <div class="ff-stat-card">
                    <div class="flex items-center justify-between">
                        <div class="ff-stat-card-icon bg-emerald-50">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="ff-stat-card-value mt-3">{{ $auditsCount }}</div>
                    <div class="ff-stat-card-label">{{ __('Audits') }}</div>
                </div>

                <div class="ff-stat-card">
                    <div class="flex items-center justify-between">
                        <div class="ff-stat-card-icon bg-amber-50">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 3.5 4 8 4s8-2 8-4V7M4 7c0 2 3.5 4 8 4s8-2 8-4M4 7c0-2 3.5-4 8-4s8 2 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                        </div>
                    </div>
                    <div class="ff-stat-card-value mt-3">{{ number_format($featuresCount) }}</div>
                    <div class="ff-stat-card-label">{{ __('Features') }}</div>
                </div>

                <div class="ff-stat-card">
                    <div class="flex items-center justify-between">
                        <div class="ff-stat-card-icon bg-purple-50">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                    <div class="ff-stat-card-value mt-3 text-base">{{ $project->client }}</div>
                    <div class="ff-stat-card-label">{{ __('Client') }}</div>
                </div>
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
                                        <span class="@switch($project->status->value)
                                            @case('draft') ff-badge-lg bg-gray-100 text-gray-700 @break
                                            @case('in_progress') ff-badge-lg bg-amber-50 text-amber-700 @break
                                            @case('audited') ff-badge-lg bg-brand-50 text-brand-700 @break
                                            @case('validated') ff-badge-lg bg-emerald-50 text-emerald-700 @break
                                            @case('archived') ff-badge-lg bg-gray-100 text-gray-700 @break
                                        @endswitch">
                                            {{ str_replace('_', ' ', ucfirst($project->status->value)) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @can('update', $project)
                        <div class="ff-card">
                            <div class="p-6">
                                <h3 class="ff-section-header mb-3">{{ __('Actions') }}</h3>
                                <div class="space-y-2">
                                    <a href="{{ route('admin.projects.datasets.import', $project) }}" class="ff-btn-secondary w-full justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        {{ __('Import Dataset') }}
                                    </a>
                                    <a href="{{ route('admin.projects.audits.index', $project) }}" class="ff-btn-secondary w-full justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        {{ __('View Audits') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>

            {{-- Network Map --}}
            @if ($project->datasets->isNotEmpty())
                <div class="ff-card">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('Network Map') }}</h3>
                        <x-project-map :project="$project" />
                    </div>
                </div>
            @endif

            {{-- Datasets --}}
            @can('update', $project)
                <div class="ff-card">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="ff-section-header">{{ __('Datasets') }}</h3>
                            <span class="ff-badge-neutral">{{ $datasetsCount }} {{ __('total') }}</span>
                        </div>

                        @if ($project->datasets->isEmpty())
                            <div class="ff-empty">
                                <div class="ff-empty-icon">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                                </div>
                                <p class="text-sm text-gray-500">{{ __('No datasets imported yet.') }}</p>
                                <a href="{{ route('admin.projects.datasets.import', $project) }}" class="ff-btn-primary mt-3">
                                    {{ __('Import your first dataset') }}
                                </a>
                            </div>
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
                                        <span class="ff-badge
                                            @switch($audit->status->value)
                                                @case('completed') ff-badge-success @break
                                                @case('running') ff-badge-brand @break
                                                @case('pending') ff-badge-warning @break
                                                @case('failed') ff-badge-danger @break
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
                                                <span class="@switch($child->status->value)
                                                    @case('draft') ff-badge-neutral @break
                                                    @case('in_progress') ff-badge-warning @break
                                                    @case('audited') ff-badge-brand @break
                                                    @case('validated') ff-badge-success @break
                                                    @case('archived') ff-badge-neutral @break
                                                @endswitch">
                                                    {{ str_replace('_', ' ', $child->status->value) }}
                                                </span>
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
