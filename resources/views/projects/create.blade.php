<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :title="__('Create Project')"
            :breadcrumbs="[['label' => __('Projects'), 'url' => route('admin.projects.index')]]"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.projects.store') }}" class="space-y-6">
                @csrf

                <div class="ff-card">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('Basic Information') }}</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="ff-label">{{ __('Name') }} <span class="text-red-500">*</span></label>
                                <input id="name" name="name" type="text" class="ff-input" value="{{ old('name') }}" required autofocus>
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="description" class="ff-label">{{ __('Description') }}</label>
                                <textarea id="description" name="description" class="ff-input" rows="3">{{ old('description') }}</textarea>
                                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ff-card">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('Location') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="client" class="ff-label">{{ __('Client') }} <span class="text-red-500">*</span></label>
                                <input id="client" name="client" type="text" class="ff-input" value="{{ old('client') }}" required>
                                @error('client') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="municipality" class="ff-label">{{ __('Municipality') }} <span class="text-red-500">*</span></label>
                                <input id="municipality" name="municipality" type="text" class="ff-input" value="{{ old('municipality') }}" required>
                                @error('municipality') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ff-card">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('Classification') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="project_type" class="ff-label">{{ __('Project Type') }} <span class="text-red-500">*</span></label>
                                <select id="project_type" name="project_type" class="ff-input">
                                    @foreach (\App\Enums\ProjectType::cases() as $type)
                                        <option value="{{ $type->value }}" {{ old('project_type') === $type->value ? 'selected' : '' }}>
                                            {{ ucfirst($type->value) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="study_phase" class="ff-label">{{ __('Study Phase') }} <span class="text-red-500">*</span></label>
                                <select id="study_phase" name="study_phase" class="ff-input">
                                    @foreach (\App\Enums\StudyPhase::cases() as $phase)
                                        <option value="{{ $phase->value }}" {{ old('study_phase') === $phase->value ? 'selected' : '' }}>
                                            {{ $phase->value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('study_phase') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ff-card">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('Hierarchy') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="gis_project_id" class="ff-label">{{ __('GIS Project ID') }} <span class="text-red-500">*</span></label>
                                <input id="gis_project_id" name="gis_project_id" type="text" class="ff-input" value="{{ old('gis_project_id') }}" required>
                                @error('gis_project_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="parent_project_id" class="ff-label">{{ __('Parent Project') }}</label>
                                <select id="parent_project_id" name="parent_project_id" class="ff-input">
                                    <option value="">{{ __('None (Transport project)') }}</option>
                                    @foreach ($transportProjects as $transport)
                                        <option value="{{ $transport->id }}" {{ old('parent_project_id') == $transport->id ? 'selected' : '' }}>
                                            {{ $transport->name }} ({{ $transport->municipality }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_project_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="ff-btn-primary">{{ __('Create Project') }}</button>
                    <a href="{{ route('admin.projects.index') }}" class="ff-btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
