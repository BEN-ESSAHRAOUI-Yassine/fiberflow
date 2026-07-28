<x-app-layout>
    <x-slot name="header">
        <h2 class="ff-page-title">{{ __('Create Project') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ff-card">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.projects.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="name" class="ff-label">{{ __('Name') }} <span class="text-red-500">*</span></label>
                            <input id="name" name="name" type="text" class="mt-1 block w-full ff-input" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="ff-label">{{ __('Description') }}</label>
                            <textarea id="description" name="description" class="mt-1 block w-full ff-input" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="client" class="ff-label">{{ __('Client') }} <span class="text-red-500">*</span></label>
                                <input id="client" name="client" type="text" class="mt-1 block w-full ff-input" value="{{ old('client') }}" required>
                                @error('client')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="municipality" class="ff-label">{{ __('Municipality') }} <span class="text-red-500">*</span></label>
                                <input id="municipality" name="municipality" type="text" class="mt-1 block w-full ff-input" value="{{ old('municipality') }}" required>
                                @error('municipality')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="project_type" class="ff-label">{{ __('Project Type') }} <span class="text-red-500">*</span></label>
                                <select id="project_type" name="project_type" class="mt-1 block w-full ff-input">
                                    @foreach (\App\Enums\ProjectType::cases() as $type)
                                        <option value="{{ $type->value }}" {{ old('project_type') === $type->value ? 'selected' : '' }}>
                                            {{ ucfirst($type->value) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="study_phase" class="ff-label">{{ __('Study Phase') }} <span class="text-red-500">*</span></label>
                                <select id="study_phase" name="study_phase" class="mt-1 block w-full ff-input">
                                    @foreach (\App\Enums\StudyPhase::cases() as $phase)
                                        <option value="{{ $phase->value }}" {{ old('study_phase') === $phase->value ? 'selected' : '' }}>
                                            {{ $phase->value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('study_phase')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="gis_project_id" class="ff-label">{{ __('GIS Project ID') }} <span class="text-red-500">*</span></label>
                                <input id="gis_project_id" name="gis_project_id" type="text" class="mt-1 block w-full ff-input" value="{{ old('gis_project_id') }}" required>
                                @error('gis_project_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="parent_project_id" class="ff-label">{{ __('Parent Project') }}</label>
                                <select id="parent_project_id" name="parent_project_id" class="mt-1 block w-full ff-input">
                                    <option value="">{{ __('None (Transport project)') }}</option>
                                    @foreach ($transportProjects as $transport)
                                        <option value="{{ $transport->id }}" {{ old('parent_project_id') == $transport->id ? 'selected' : '' }}>
                                            {{ $transport->name }} ({{ $transport->municipality }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_project_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" class="ff-btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('admin.projects.index') }}" class="ff-btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
