<x-app-layout>
    <x-slot name="header">
        <h2 class="ff-page-title">{{ $project->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($project->trashed())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    {{ __('This project is archived.') }}
                </div>
            @endif

            <div class="ff-card">
                <div class="p-6">
                    <dl class="ff-dl">
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Name') }}</dt>
                            <dd class="ff-dl-value">{{ $project->name }}</dd>
                        </div>
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Description') }}</dt>
                            <dd class="ff-dl-value">{{ $project->description ?? __('N/A') }}</dd>
                        </div>
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Client') }}</dt>
                            <dd class="ff-dl-value">{{ $project->client }}</dd>
                        </div>
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Municipality') }}</dt>
                            <dd class="ff-dl-value">{{ $project->municipality }}</dd>
                        </div>
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Project Type') }}</dt>
                            <dd class="ff-dl-value">
                                <span class="ff-badge-brand">{{ ucfirst($project->project_type->value) }}</span>
                            </dd>
                        </div>
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Study Phase') }}</dt>
                            <dd class="ff-dl-value">{{ $project->study_phase->value }}</dd>
                        </div>
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Status') }}</dt>
                            <dd class="ff-dl-value">
                                <span class="@switch($project->status->value)
                                    @case('draft') ff-badge-neutral @break
                                    @case('in_progress') ff-badge-warning @break
                                    @case('audited') ff-badge-brand @break
                                    @case('validated') ff-badge-success @break
                                    @case('archived') ff-badge-neutral @break
                                @endswitch">
                                    {{ str_replace('_', ' ', $project->status->value) }}
                                </span>
                            </dd>
                        </div>
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('GIS Project ID') }}</dt>
                            <dd class="ff-dl-value">{{ $project->gis_project_id }}</dd>
                        </div>
                        @if ($project->parentProject)
                            <div class="ff-dl-row">
                                <dt class="ff-dl-label">{{ __('Parent Project') }}</dt>
                                <dd class="ff-dl-value">
                                    <a href="{{ route('admin.projects.show', $project->parentProject) }}" class="text-brand-600 hover:text-brand-700">{{ $project->parentProject->name }}</a>
                                </dd>
                            </div>
                        @endif
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Created By') }}</dt>
                            <dd class="ff-dl-value">{{ $project->creator?->name ?? $project->created_by }}</dd>
                        </div>
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Created') }}</dt>
                            <dd class="ff-dl-value">{{ $project->created_at->format('M j, Y g:i A') }}</dd>
                        </div>
                        <div class="ff-dl-row">
                            <dt class="ff-dl-label">{{ __('Updated') }}</dt>
                            <dd class="ff-dl-value">{{ $project->updated_at->format('M j, Y g:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @can('update', $project)
                <div class="ff-section mt-8">
                    <div class="ff-section-header">
                        <h3 class="ff-page-title text-base">{{ __('Datasets') }}</h3>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.projects.audits.index', $project) }}" class="ff-btn-secondary">
                                {{ __('Audits') }}
                            </a>
                            <a href="{{ route('admin.projects.datasets.import', $project) }}" class="ff-btn-primary">
                                {{ __('Import Dataset') }}
                            </a>
                        </div>
                    </div>

                    @if ($project->datasets->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No datasets imported yet.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="ff-table w-full">
                                <thead>
                                    <tr>
                                        <th>{{ __('Imported At') }}</th>
                                        <th>{{ __('# Features') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($project->datasets as $dataset)
                                        <tr>
                                            <td class="text-gray-900">{{ $dataset->imported_at->format('M j, Y g:i A') }}</td>
                                            <td class="text-gray-700">{{ collect($dataset->geojson)->flatten()->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endcan

            @if ($project->datasets->isNotEmpty())
                <div class="ff-section mt-8">
                    <div class="ff-section-header">
                        <h3 class="ff-page-title text-base">{{ __('Network Map') }}</h3>
                    </div>
                    <x-project-map :project="$project" />
                </div>
            @endif

            @if ($project->childProjects->isNotEmpty())
                <div class="ff-section mt-8">
                    <div class="ff-section-header">
                        <h3 class="ff-page-title text-base">{{ __('Child Projects') }}</h3>
                    </div>
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
            @endif

            <div class="mt-8 flex items-center gap-4">
                @unless ($project->trashed())
                    @can('update', $project)
                        <a href="{{ route('admin.projects.edit', $project) }}" class="ff-btn-primary">
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
                <a href="{{ route('admin.projects.index') }}" class="ff-btn-secondary">
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
