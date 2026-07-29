<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Create your account') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('Get started with FiberFlow.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="ff-label">{{ __('Name') }}</label>
            <input id="name" type="text" name="name" class="ff-input" value="{{ old('name') }}" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <label for="email" class="ff-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" class="ff-input" value="{{ old('email') }}" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="ff-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="ff-input" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <label for="password_confirmation" class="ff-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="ff-input" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="ff-btn-primary w-full justify-center">
            {{ __('Create Account') }}
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700 font-medium">{{ __('Sign in') }}</a>
        </p>
    </form>
</x-guest-layout>
