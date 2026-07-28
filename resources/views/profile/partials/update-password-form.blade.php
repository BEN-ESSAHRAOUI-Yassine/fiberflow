<section>
    <header>
        <h2 class="ff-section-header">
            {{ __('Update Password') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="ff-label">{{ __('Current Password') }}</label>
            <input id="current_password" name="current_password" type="password" class="ff-input mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="new_password" class="ff-label">{{ __('New Password') }}</label>
            <input id="new_password" name="new_password" type="password" class="ff-input mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('new_password')" class="mt-2" />
        </div>

        <div>
            <label for="new_password_confirmation" class="ff-label">{{ __('Confirm Password') }}</label>
            <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="ff-input mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('new_password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="ff-btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
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
