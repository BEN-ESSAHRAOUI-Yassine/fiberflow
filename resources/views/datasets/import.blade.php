<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :title="__('Import Dataset')"
            :breadcrumbs="[['label' => $project->name, 'url' => route('admin.projects.show', $project)]]"
        />
    </x-slot>

    <div class="py-8" x-data="{ importing: false }">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <form
                action="{{ route('admin.projects.datasets.import.store', $project) }}"
                method="POST"
                @submit="importing = true"
            >
                @csrf

                <div class="ff-card">
                    <div class="p-6 space-y-6">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">{{ __('GIS Connection') }}</h2>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Connect to the PostGIS server hosting the network data. The password is used for this import only and is never stored.') }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label for="host" class="ff-label">{{ __('Host') }}</label>
                                <input type="text" name="host" id="host" value="{{ old('host', $project->gis_host ?? '127.0.0.1') }}" class="ff-input" placeholder="127.0.0.1">
                                @error('host')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="port" class="ff-label">{{ __('Port') }}</label>
                                <input type="number" name="port" id="port" value="{{ old('port', $project->gis_port ?? '5432') }}" class="ff-input" placeholder="5432">
                                @error('port')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="database" class="ff-label">{{ __('Database') }}</label>
                            <input type="text" name="database" id="database" value="{{ old('database', $project->gis_database) }}" class="ff-input" placeholder="fiberflow_gis">
                            @error('database')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="username" class="ff-label">{{ __('Username') }}</label>
                                <input type="text" name="username" id="username" value="{{ old('username', $project->gis_username) }}" class="ff-input" placeholder="fiberflow">
                                @error('username')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="ff-label">{{ __('Password') }}</label>
                                <input type="password" name="password" id="password" class="ff-input" placeholder="••••••••" autocomplete="new-password">
                                @error('password')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @error('connection')
                            <div class="rounded-lg border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-700">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="flex items-center gap-3 pt-1">
                            <button
                                type="submit"
                                formaction="{{ route('admin.projects.datasets.test-connection', $project) }}"
                                class="ff-btn-secondary"
                                :disabled="importing"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ __('Test Connection') }}
                            </button>
                            @if (session('connection_ok'))
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-success-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $schemas->isNotEmpty() ? __('Connection verified') : __('Connected, no candidate schema found') }}
                                </span>
                            @endif
                        </div>

                        @if ($schemas->isNotEmpty())
                            <div class="border-t border-surface-200 pt-6">
                                <div>
                                    <label for="schema" class="ff-label">{{ __('Dataset Source') }}</label>
                                    <select name="schema" id="schema" class="ff-input">
                                        <option value="">-- {{ __('Select a schema') }} --</option>
                                        @foreach ($schemas as $schema)
                                            <option value="{{ $schema->schema }}" @selected(old('schema') === $schema->schema)>
                                                {{ $schema->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('schema')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-4 mt-6">
                    <button type="submit" class="ff-btn-primary" :disabled="importing">
                        <span x-show="importing" class="inline-flex">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                        </span>
                        <svg x-show="!importing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        {{ __('Import') }}
                    </button>
                    <a href="{{ route('admin.projects.show', $project) }}" class="ff-btn-secondary" :disabled="importing">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
