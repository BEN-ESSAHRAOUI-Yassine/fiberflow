<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Sign in to FiberFlow') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('Enter your credentials to access your account.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="ff-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" class="ff-input" value="{{ old('email') }}" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="ff-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="ff-input" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-surface-200 text-brand-600 shadow-surface focus:ring-brand-500" name="remember">
                <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <button type="submit" class="ff-btn-primary w-full justify-center">
            {{ __('Sign in') }}
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 font-medium">{{ __('Register') }}</a>
        </p>
    </form>
</x-guest-layout>
