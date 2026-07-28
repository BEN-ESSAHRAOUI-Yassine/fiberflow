<section>
    <header>
        <h2 class="ff-section-header">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="ff-label">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="ff-input mt-1 block w-full" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="ff-label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="ff-input mt-1 block w-full" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="ff-btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-500"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
