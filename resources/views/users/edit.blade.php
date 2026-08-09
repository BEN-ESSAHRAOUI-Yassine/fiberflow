<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :title="__('Edit User')"
            :breadcrumbs="[
                ['label' => __('Users'), 'url' => route('admin.users.index')],
                ['label' => $user->name, 'url' => route('admin.users.show', $user)],
            ]"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="ff-card">
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="name" class="ff-label">{{ __('Name') }} <span class="text-red-500">*</span></label>
                            <input id="name" name="name" type="text" class="ff-input" value="{{ old('name', $user->name) }}" required autofocus />
                            <x-input-error class="mt-1" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <label for="email" class="ff-label">{{ __('Email') }} <span class="text-red-500">*</span></label>
                            <input id="email" name="email" type="email" class="ff-input" value="{{ old('email', $user->email) }}" required />
                            <x-input-error class="mt-1" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <label for="password" class="ff-label">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password" class="ff-input" />
                            <p class="mt-1 text-xs text-gray-400">{{ __('Leave blank to keep current password.') }}</p>
                            <x-input-error class="mt-1" :messages="$errors->get('password')" />
                        </div>

                        <div>
                            <label for="role" class="ff-label">{{ __('Role') }} <span class="text-red-500">*</span></label>
                            <select id="role" name="role" class="ff-input">
                                @foreach (\App\Enums\UserRole::cases() as $role)
                                    <option value="{{ $role->value }}" {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                        {{ ucfirst($role->value) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-1" :messages="$errors->get('role')" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="ff-btn-primary">{{ __('Update User') }}</button>
                    <a href="{{ route('admin.users.show', $user) }}" class="ff-btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
