<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="ff-page-title">{{ __('Projects') }}</h2>
            @can('create', App\Models\Project::class)
                <a href="{{ route('admin.projects.create') }}" class="ff-btn-primary">
                    {{ __('Create Project') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="ff-card">
                <div class="p-6 border-b border-surface-200">
                    <form method="GET" action="{{ route('admin.projects.index') }}"
                          x-data="{ search: '{{ request('search') }}' }">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                            <div class="lg:col-span-2">
                                <label for="search" class="ff-label">{{ __('Search') }}</label>
                                <input type="text" name="search" id="search" x-model="search"
                                       x-on:input.debounce.300ms="$el.form.requestSubmit()"
                                       value="{{ request('search') }}"
                                       placeholder="{{ __('Search projects...') }}"
                                       class="mt-1 block w-full ff-input">
                            </div>

                            <div>
                                <label for="project_type" class="ff-label">{{ __('Type') }}</label>
                                <select name="project_type" id="project_type"
                                        x-on:change="$el.form.requestSubmit()"
                                        class="mt-1 block w-full ff-input">
                                    <option value="">{{ __('All Types') }}</option>
                                    @foreach (App\Enums\ProjectType::values() as $type)
                                        <option value="{{ $type }}" @selected(request('project_type') === $type)>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="status" class="ff-label">{{ __('Status') }}</label>
                                <select name="status" id="status"
                                        x-on:change="$el.form.requestSubmit()"
                                        class="mt-1 block w-full ff-input">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach (App\Enums\ProjectStatus::values() as $s)
                                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ str_replace('_', ' ', $s) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="study_phase" class="ff-label">{{ __('Phase') }}</label>
                                <select name="study_phase" id="study_phase"
                                        x-on:change="$el.form.requestSubmit()"
                                        class="mt-1 block w-full ff-input">
                                    <option value="">{{ __('All Phases') }}</option>
                                    @foreach (App\Enums\StudyPhase::values() as $phase)
                                        <option value="{{ $phase }}" @selected(request('study_phase') === $phase)>{{ $phase }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="client" class="ff-label">{{ __('Client') }}</label>
                                <input type="text" name="client" id="client"
                                       x-on:input.debounce.500ms="$el.form.requestSubmit()"
                                       value="{{ request('client') }}"
                                       placeholder="{{ __('Filter by client...') }}"
                                       class="mt-1 block w-full ff-input">
                            </div>

                            @can('create', App\Models\Project::class)
                                <div class="flex items-end pb-1">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="archived" value="1"
                                               x-on:change="$el.form.requestSubmit()"
                                               @checked(request('archived'))>
                                        <span class="text-sm text-gray-700">{{ __('Archived only') }}</span>
                                    </label>
                                </div>
                            @endcan
                        </div>

                        @if (request()->has('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if (request()->has('direction'))
                            <input type="hidden" name="direction" value="{{ request('direction') }}">
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="ff-table w-full">
                        <thead>
                            <tr>
                                @php
                                    $currentSort = request('sort');
                                    $currentDir = request('direction');
                                @endphp
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => $currentSort === 'name' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Name') }}
                                        @if ($currentSort === 'name')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'client', 'direction' => $currentSort === 'client' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Client') }}
                                        @if ($currentSort === 'client')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'municipality', 'direction' => $currentSort === 'municipality' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Municipality') }}
                                        @if ($currentSort === 'municipality')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'project_type', 'direction' => $currentSort === 'project_type' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Type') }}
                                        @if ($currentSort === 'project_type')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'study_phase', 'direction' => $currentSort === 'study_phase' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Phase') }}
                                        @if ($currentSort === 'study_phase')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'status', 'direction' => $currentSort === 'status' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Status') }}
                                        @if ($currentSort === 'status')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $project)
                                <tr class="{{ $project->trashed() ? 'opacity-50' : '' }}">
                                    <td>
                                        <a href="{{ route('admin.projects.show', $project) }}" class="text-brand-600 hover:text-brand-700 font-medium">{{ $project->name }}</a>
                                    </td>
                                    <td class="text-gray-700">{{ $project->client }}</td>
                                    <td class="text-gray-700">{{ $project->municipality }}</td>
                                    <td>
                                        <span class="ff-badge-brand">
                                            {{ $project->project_type->value }}
                                        </span>
                                    </td>
                                    <td class="text-gray-700">{{ $project->study_phase->value }}</td>
                                    <td>
                                        <span class="@switch($project->status->value)
                                            @case('draft') ff-badge-neutral @break
                                            @case('in_progress') ff-badge-warning @break
                                            @case('audited') ff-badge-brand @break
                                            @case('validated') ff-badge-success @break
                                            @case('archived') ff-badge-neutral @break
                                        @endswitch">
                                            {{ str_replace('_', ' ', $project->status->value) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        @if ($project->trashed())
                                            @can('restore', $project)
                                                <form action="{{ route('admin.projects.restore', $project) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-emerald-600 hover:text-emerald-700">{{ __('Restore') }}</button>
                                                </form>
                                            @endcan
                                        @else
                                            @can('update', $project)
                                                <a href="{{ route('admin.projects.edit', $project) }}" class="text-brand-600 hover:text-brand-700">{{ __('Edit') }}</a>
                                            @endcan
                                            @can('delete', $project)
                                                <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline ml-3">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('{{ __('Archive this project?') }}')">{{ __('Archive') }}</button>
                                                </form>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-gray-500 py-12">{{ __('No projects found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-surface-100">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
