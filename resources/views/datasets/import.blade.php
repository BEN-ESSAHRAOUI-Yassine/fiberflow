<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Import Dataset') }} — {{ $project->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.projects.datasets.import.store', $project) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="schema" class="block text-sm font-medium text-gray-700">{{ __('Dataset Source') }}</label>
                            <select name="schema" id="schema" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <x-primary-button>{{ __('Import') }}</x-primary-button>
                            <a href="{{ route('admin.projects.show', $project) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
