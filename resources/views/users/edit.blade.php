<x-app-layout>
    <x-slot name="header">
        <h2 class="ff-page-title">{{ __('Edit User') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ff-card">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="ff-label">{{ __('Name') }} <span class="text-red-500">*</span></label>
                            <input id="name" name="name" type="text" class="ff-input mt-1 block w-full" value="{{ old('name', $user->name) }}" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <label for="email" class="ff-label">{{ __('Email') }} <span class="text-red-500">*</span></label>
                            <input id="email" name="email" type="email" class="ff-input mt-1 block w-full" value="{{ old('email', $user->email) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <label for="password" class="ff-label">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password" class="ff-input mt-1 block w-full" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        <div>
                            <label for="role" class="ff-label">{{ __('Role') }} <span class="text-red-500">*</span></label>
                            <select id="role" name="role" class="ff-input mt-1 block w-full">
                                @foreach (\App\Enums\UserRole::cases() as $role)
                                    <option value="{{ $role->value }}" {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                        {{ ucfirst($role->value) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('role')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="ff-btn-primary">{{ __('Update') }}</button>
                            <a href="{{ route('admin.users.index') }}" class="ff-btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
