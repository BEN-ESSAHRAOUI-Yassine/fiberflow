<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Project') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.projects.update', $project) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Name')" required />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $project->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description', $project->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="client" :value="__('Client')" required />
                                <x-text-input id="client" name="client" type="text" class="mt-1 block w-full" :value="old('client', $project->client)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('client')" />
                            </div>

                            <div>
                                <x-input-label for="municipality" :value="__('Municipality')" required />
                                <x-text-input id="municipality" name="municipality" type="text" class="mt-1 block w-full" :value="old('municipality', $project->municipality)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('municipality')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="project_type" :value="__('Project Type')" />
                                <x-text-input id="project_type" type="text" class="mt-1 block w-full bg-gray-100" :value="ucfirst($project->project_type->value)" disabled />
                                <p class="mt-1 text-xs text-gray-500">{{ __('Project type cannot be changed after creation.') }}</p>
                            </div>

                            <div>
                                <x-input-label for="study_phase" :value="__('Study Phase')" required />
                                <select id="study_phase" name="study_phase" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach (\App\Enums\StudyPhase::cases() as $phase)
                                        <option value="{{ $phase->value }}" {{ old('study_phase', $project->study_phase->value) === $phase->value ? 'selected' : '' }}>
                                            {{ $phase->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('study_phase')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="gis_project_id" :value="__('GIS Project ID')" required />
                                <x-text-input id="gis_project_id" name="gis_project_id" type="text" class="mt-1 block w-full" :value="old('gis_project_id', $project->gis_project_id)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('gis_project_id')" />
                            </div>

                            <div>
                                <x-input-label for="parent_project_id" :value="__('Parent Project')" />
                                <select id="parent_project_id" name="parent_project_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('None (Transport project)') }}</option>
                                    @foreach ($transportProjects as $transport)
                                        <option value="{{ $transport->id }}" {{ old('parent_project_id', $project->parent_project_id) == $transport->id ? 'selected' : '' }}>
                                            {{ $transport->name }} ({{ $transport->municipality }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('parent_project_id')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update') }}</x-primary-button>
                            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
