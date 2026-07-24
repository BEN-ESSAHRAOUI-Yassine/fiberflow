<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Projects') }}</h2>
            @can('create', App\Models\Project::class)
                <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Create Project') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" action="{{ route('admin.projects.index') }}"
                          x-data="{ search: '{{ request('search') }}' }">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                            <div class="lg:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                                <input type="text" name="search" id="search" x-model="search"
                                       x-on:input.debounce.300ms="$el.form.requestSubmit()"
                                       value="{{ request('search') }}"
                                       placeholder="{{ __('Search projects...') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>

                            <div>
                                <label for="project_type" class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
                                <select name="project_type" id="project_type"
                                        x-on:change="$el.form.requestSubmit()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">{{ __('All Types') }}</option>
                                    @foreach (App\Enums\ProjectType::values() as $type)
                                        <option value="{{ $type }}" @selected(request('project_type') === $type)>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                <select name="status" id="status"
                                        x-on:change="$el.form.requestSubmit()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach (App\Enums\ProjectStatus::values() as $s)
                                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ str_replace('_', ' ', $s) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="study_phase" class="block text-sm font-medium text-gray-700">{{ __('Phase') }}</label>
                                <select name="study_phase" id="study_phase"
                                        x-on:change="$el.form.requestSubmit()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">{{ __('All Phases') }}</option>
                                    @foreach (App\Enums\StudyPhase::values() as $phase)
                                        <option value="{{ $phase }}" @selected(request('study_phase') === $phase)>{{ $phase }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="client" class="block text-sm font-medium text-gray-700">{{ __('Client') }}</label>
                                <input type="text" name="client" id="client"
                                       x-on:input.debounce.500ms="$el.form.requestSubmit()"
                                       value="{{ request('client') }}"
                                       placeholder="{{ __('Filter by client...') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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

                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @php
                                    $currentSort = request('sort');
                                    $currentDir = request('direction');
                                @endphp
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => $currentSort === 'name' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-700">
                                        {{ __('Name') }}
                                        @if ($currentSort === 'name')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'client', 'direction' => $currentSort === 'client' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-700">
                                        {{ __('Client') }}
                                        @if ($currentSort === 'client')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'municipality', 'direction' => $currentSort === 'municipality' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-700">
                                        {{ __('Municipality') }}
                                        @if ($currentSort === 'municipality')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'project_type', 'direction' => $currentSort === 'project_type' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-700">
                                        {{ __('Type') }}
                                        @if ($currentSort === 'project_type')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'study_phase', 'direction' => $currentSort === 'study_phase' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-700">
                                        {{ __('Phase') }}
                                        @if ($currentSort === 'study_phase')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'status', 'direction' => $currentSort === 'status' && $currentDir === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:text-gray-700">
                                        {{ __('Status') }}
                                        @if ($currentSort === 'status')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($projects as $project)
                                <tr class="{{ $project->trashed() ? 'bg-gray-50 text-gray-400' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $project->trashed() ? '' : 'text-gray-900' }}">
                                        <a href="{{ route('admin.projects.show', $project) }}" class="{{ $project->trashed() ? 'text-gray-400' : 'text-indigo-600 hover:text-indigo-900' }}">{{ $project->name }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $project->trashed() ? '' : 'text-gray-500' }}">{{ $project->client }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $project->trashed() ? '' : 'text-gray-500' }}">{{ $project->municipality }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $project->project_type->value === 'transport' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $project->project_type->value }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $project->trashed() ? '' : 'text-gray-500' }}">{{ $project->study_phase->value }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @switch($project->status->value)
                                                @case('draft') bg-gray-100 text-gray-800 @break
                                                @case('in_progress') bg-yellow-100 text-yellow-800 @break
                                                @case('audited') bg-indigo-100 text-indigo-800 @break
                                                @case('validated') bg-green-100 text-green-800 @break
                                                @case('archived') bg-red-100 text-red-800 @break
                                            @endswitch
                                        ">
                                            {{ str_replace('_', ' ', $project->status->value) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        @if ($project->trashed())
                                            @can('restore', $project)
                                                <form action="{{ route('admin.projects.restore', $project) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-green-600 hover:text-green-900">{{ __('Restore') }}</button>
                                                </form>
                                            @endcan
                                        @else
                                            @can('update', $project)
                                                <a href="{{ route('admin.projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                            @endcan
                                            @can('delete', $project)
                                                <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Archive this project?') }}')">{{ __('Archive') }}</button>
                                                </form>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No projects found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
