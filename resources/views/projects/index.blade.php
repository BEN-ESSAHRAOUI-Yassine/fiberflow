<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="__('Projects')">
            <x-slot name="actions">
                @can('create', App\Models\Project::class)
                    <a href="{{ route('admin.projects.create') }}" class="ff-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Create Project') }}
                    </a>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif

            {{-- Filters --}}
            <div class="ff-card">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.projects.index') }}" x-data="{ search: '{{ request('search') }}' }">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                            <div class="lg:col-span-2">
                                <label for="search" class="sr-only">{{ __('Search') }}</label>
                                <input id="search" type="text" name="search" x-model="search"
                                       x-on:input.debounce.300ms="$el.form.requestSubmit()"
                                       value="{{ request('search') }}"
                                       placeholder="{{ __('Search projects...') }}"
                                       class="ff-input">
                            </div>
                            <div>
                                <label for="project_type" class="sr-only">{{ __('Type') }}</label>
                                <select id="project_type" name="project_type" x-on:change="$el.form.requestSubmit()" class="ff-input">
                                    <option value="">{{ __('All Types') }}</option>
                                    @foreach (App\Enums\ProjectType::values() as $type)
                                        <option value="{{ $type }}" @selected(request('project_type') === $type)>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status" class="sr-only">{{ __('Status') }}</label>
                                <select id="status" name="status" x-on:change="$el.form.requestSubmit()" class="ff-input">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach (App\Enums\ProjectStatus::values() as $s)
                                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ str_replace('_', ' ', $s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="study_phase" class="sr-only">{{ __('Phase') }}</label>
                                <select id="study_phase" name="study_phase" x-on:change="$el.form.requestSubmit()" class="ff-input">
                                    <option value="">{{ __('All Phases') }}</option>
                                    @foreach (App\Enums\StudyPhase::values() as $phase)
                                        <option value="{{ $phase }}" @selected(request('study_phase') === $phase)>{{ $phase }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="client" class="sr-only">{{ __('Client') }}</label>
                                <input id="client" type="text" name="client"
                                       value="{{ request('client') }}"
                                       placeholder="{{ __('Client...') }}"
                                       x-on:input.debounce.500ms="$el.form.requestSubmit()"
                                       class="ff-input">
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <p class="text-sm text-gray-500">{{ $projects->total() }} {{ __('projects') }}</p>
                            @if (request()->hasAny(['search', 'project_type', 'status', 'study_phase', 'client', 'sort']))
                                <a href="{{ route('admin.projects.index') }}" class="ff-btn-ghost text-sm">{{ __('Clear filters') }}</a>
                            @endif
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
                                <x-th-sortable name="name" :label="__('Name')" />
                                <x-th-sortable name="client" :label="__('Client')" />
                                <x-th-sortable name="municipality" :label="__('Municipality')" />
                                <x-th-sortable name="project_type" :label="__('Type')" />
                                <x-th-sortable name="study_phase" :label="__('Phase')" />
                                <x-th-sortable name="status" :label="__('Status')" />
                                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
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
                                    <td><span class="font-mono text-xs text-gray-500 uppercase tracking-wide">{{ $project->project_type->value }}</span></td>
                                    <td><span class="font-mono text-xs text-gray-500">{{ $project->study_phase->value }}</span></td>
                                    <td>
                                        @php($personalStatus = $project->personalStatus(auth()->user()))
                                        <x-status-badge :status="$personalStatus->value" :title="$personalStatus !== $project->status ? __('You completed an audit on this project') : null">{{ str_replace('_', ' ', $personalStatus->value) }}</x-status-badge>
                                    </td>
                                    <td class="text-right">
                                        @if ($project->trashed())
                                            @can('restore', $project)
                                                <form action="{{ route('admin.projects.restore', $project) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="ff-btn-ghost text-success-600 hover:text-success-700 text-sm">{{ __('Restore') }}</button>
                                                </form>
                                            @endcan
                                        @else
                                            @can('update', $project)
                                                <a href="{{ route('admin.projects.edit', $project) }}" class="ff-btn-ghost text-sm">{{ __('Edit') }}</a>
                                            @endcan
                                            @can('delete', $project)
                                                <x-confirm-modal
                                                    title="{{ __('Archive project?') }}"
                                                    message="{{ __('This will archive the project. You can restore it later.') }}"
                                                    :action="route('admin.projects.destroy', $project)"
                                                    method="DELETE">
                                                    <x-slot name="trigger">
                                                        <button type="button" class="ff-btn-ghost text-danger-600 hover:text-danger-700 text-sm">{{ __('Archive') }}</button>
                                                    </x-slot>
                                                    {{ __('Archive') }}
                                                </x-confirm-modal>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <x-empty-state
                                            :title="__('No projects found')"
                                            :description="__('Try adjusting your filters, or create a new project to get started.')">
                                            <x-slot name="icon">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                            </x-slot>
                                            @can('create', App\Models\Project::class)
                                                <a href="{{ route('admin.projects.create') }}" class="ff-btn-primary">{{ __('Create Project') }}</a>
                                            @endcan
                                        </x-empty-state>
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
