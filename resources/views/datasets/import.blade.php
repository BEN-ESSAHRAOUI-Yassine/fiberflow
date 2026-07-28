<x-app-layout>
    <x-slot name="header">
        <h2 class="ff-page-title">{{ __('Import Dataset') }} — {{ $project->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ff-card">
                <div class="p-6">
                    <form action="{{ route('admin.projects.datasets.import.store', $project) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="schema" class="ff-label">{{ __('Dataset Source') }}</label>
                            <select name="schema" id="schema" class="ff-input mt-1 block w-full">
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

                        <div class="flex items-center gap-4">
                            <button type="submit" class="ff-btn-primary">{{ __('Import') }}</button>
                            <a href="{{ route('admin.projects.show', $project) }}" class="ff-btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
