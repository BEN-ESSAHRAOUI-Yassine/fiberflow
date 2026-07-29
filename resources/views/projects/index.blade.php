<x-app-layout>
    <x-slot name="header">
        <div class="ff-page-header-actions">
            <div>
                <h1 class="ff-page-title text-2xl">{{ __('Projects') }}</h1>
            </div>
            @can('create', App\Models\Project::class)
                <a href="{{ route('admin.projects.create') }}" class="ff-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Create Project') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="ff-card">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.projects.index') }}" x-data="{ search: '{{ request('search') }}' }">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                            <div class="lg:col-span-2">
                                <input type="text" name="search" x-model="search"
                                       x-on:input.debounce.300ms="$el.form.requestSubmit()"
                                       value="{{ request('search') }}"
                                       placeholder="{{ __('Search projects...') }}"
                                       class="ff-input">
                            </div>
                            <div>
                                <select name="project_type" x-on:change="$el.form.requestSubmit()" class="ff-input">
                                    <option value="">{{ __('All Types') }}</option>
                                    @foreach (App\Enums\ProjectType::values() as $type)
                                        <option value="{{ $type }}" @selected(request('project_type') === $type)>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="status" x-on:change="$el.form.requestSubmit()" class="ff-input">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach (App\Enums\ProjectStatus::values() as $s)
                                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ str_replace('_', ' ', $s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="study_phase" x-on:change="$el.form.requestSubmit()" class="ff-input">
                                    <option value="">{{ __('All Phases') }}</option>
                                    @foreach (App\Enums\StudyPhase::values() as $phase)
                                        <option value="{{ $phase }}" @selected(request('study_phase') === $phase)>{{ $phase }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="text" name="client"
                                       value="{{ request('client') }}"
                                       placeholder="{{ __('Client...') }}"
                                       x-on:input.debounce.500ms="$el.form.requestSubmit()"
                                       class="ff-input">
                            </div>
                        </div>
                        @if (request()->has('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if (request()->has('direction'))
                            <input type="hidden" name="direction" value="{{ request('direction') }}">
                        @endif
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="ff-card">
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
                                        @if ($currentSort === 'name') <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'client', 'direction' => $currentSort === 'client' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Client') }}
                                        @if ($currentSort === 'client') <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'municipality', 'direction' => $currentSort === 'municipality' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Municipality') }}
                                        @if ($currentSort === 'municipality') <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'project_type', 'direction' => $currentSort === 'project_type' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Type') }}
                                        @if ($currentSort === 'project_type') <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'study_phase', 'direction' => $currentSort === 'study_phase' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Phase') }}
                                        @if ($currentSort === 'study_phase') <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'status', 'direction' => $currentSort === 'status' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-900">
                                        {{ __('Status') }}
                                        @if ($currentSort === 'status') <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span> @endif
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
                                    <td><span class="ff-badge-brand">{{ $project->project_type->value }}</span></td>
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
                                                    <button type="submit" class="ff-btn-ghost text-emerald-600 hover:text-emerald-700 text-sm">{{ __('Restore') }}</button>
                                                </form>
                                            @endcan
                                        @else
                                            @can('update', $project)
                                                <a href="{{ route('admin.projects.edit', $project) }}" class="ff-btn-ghost text-sm">{{ __('Edit') }}</a>
                                            @endcan
                                            @can('delete', $project)
                                                <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="ff-btn-ghost text-red-600 hover:text-red-700 text-sm" onclick="return confirm('{{ __('Archive this project?') }}')">{{ __('Archive') }}</button>
                                                </form>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="ff-empty py-12">
                                            <div class="ff-empty-icon">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                            </div>
                                            <p class="text-sm text-gray-500">{{ __('No projects found.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-surface-100">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
