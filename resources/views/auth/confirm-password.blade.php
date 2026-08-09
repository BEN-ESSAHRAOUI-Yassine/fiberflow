<x-guest-layout>
    <div class="mb-7">
        <p class="ff-eyebrow mb-2">FiberFlow · Secure</p>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ __('Confirm your password') }}</h2>
        <p class="text-sm text-gray-500 mt-1.5">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="ff-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="ff-input" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <button type="submit" class="ff-btn-primary w-full justify-center active:scale-[0.99]">
            {{ __('Confirm') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12"/></svg>
        </button>
    </form>
</x-guest-layout>
