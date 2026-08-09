<x-guest-layout>
    <div class="mb-7">
        <p class="ff-eyebrow mb-2">FiberFlow · Access</p>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ __('Sign in') }}</h2>
        <p class="text-sm text-gray-500 mt-1.5">{{ __('Enter your credentials to access your workspace.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="ff-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" class="ff-input" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@operator.ma" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="ff-label">{{ __('Password') }}</label>
            <div class="relative" x-data="{ show: false }">
                <input id="password" type="password" name="password" class="ff-input pr-10" required autocomplete="current-password" placeholder="••••••••" :type="show ? 'text' : 'password'" />
                <button type="button"
                        x-on:click="show = !show"
                        x-bind:aria-label="show ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-gray-400 hover:text-brand-600 hover:bg-info-50 transition-colors"
                        x-bind:aria-pressed="show">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-surface-200 text-brand-600 shadow-surface focus:ring-brand-500" name="remember">
                <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <button type="submit" class="ff-btn-primary w-full justify-center active:scale-[0.99]">
            {{ __('Sign in') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12"/></svg>
        </button>

        <p class="text-center text-sm text-gray-500 mt-5">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 font-medium">{{ __('Register') }}</a>
        </p>
    </form>
</x-guest-layout>
