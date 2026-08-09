<x-guest-layout>
    <div class="mb-7">
        <p class="ff-eyebrow mb-2">FiberFlow · Provision</p>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ __('Create your account') }}</h2>
        <p class="text-sm text-gray-500 mt-1.5">{{ __('Set up your workspace in under a minute.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="ff-label">{{ __('Name') }}</label>
            <input id="name" type="text" name="name" class="ff-input" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Ada Lovelace" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <label for="email" class="ff-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" class="ff-input" value="{{ old('email') }}" required autocomplete="username" placeholder="you@operator.ma" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="ff-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="ff-input" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <label for="password_confirmation" class="ff-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="ff-input" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="ff-btn-primary w-full justify-center active:scale-[0.99]">
            {{ __('Create Account') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12"/></svg>
        </button>

        <p class="text-center text-sm text-gray-500 mt-5">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700 font-medium">{{ __('Sign in') }}</a>
        </p>
    </form>
</x-guest-layout>
