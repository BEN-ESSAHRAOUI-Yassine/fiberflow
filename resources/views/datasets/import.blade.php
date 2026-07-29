<x-app-layout>
    <x-slot name="header">
        <div class="ff-page-header-actions">
            <div>
                <div class="ff-breadcrumb">
                    <a href="{{ route('admin.projects.show', $project) }}">{{ $project->name }}</a>
                    <span class="ff-breadcrumb-sep">/</span>
                    <span class="text-gray-900">{{ __('Import Dataset') }}</span>
                </div>
                <h1 class="ff-page-title text-2xl">{{ __('Import Dataset') }}</h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('admin.projects.datasets.import.store', $project) }}" method="POST">
                @csrf

                <div class="ff-card">
                    <div class="p-6 space-y-6">
                        <div class="ff-empty py-8 border-2 border-dashed border-surface-300 rounded-xl">
                            <div class="ff-empty-icon">
                                <svg class="w-10 h-10 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            </div>
                            <p class="text-sm text-gray-500">{{ __('Select a GeoJSON dataset source to import') }}</p>
                        </div>

                        <div>
                            <label for="schema" class="ff-label">{{ __('Dataset Source') }}</label>
                            <select name="schema" id="schema" class="ff-input">
                                <option value="">-- {{ __('Select a folder') }} --</option>
                                @foreach ($schemas as $schema)
                                    <option value="{{ $schema->schema }}" @selected(old('schema') === $schema->schema)>
                                        {{ $schema->label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('schema')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 mt-6">
                    <button type="submit" class="ff-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        {{ __('Import') }}
                    </button>
                    <a href="{{ route('admin.projects.show', $project) }}" class="ff-btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
