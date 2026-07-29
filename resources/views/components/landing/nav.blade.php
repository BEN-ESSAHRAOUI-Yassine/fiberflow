<nav id="landing-nav" class="ff-nav ff-nav-transparent">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-18">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5 shrink-0">
                <x-application-logo class="w-8 h-8" />
                <span class="text-base font-semibold text-gray-900">FiberFlow</span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden lg:flex items-center gap-8">
                <a href="#features" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Features</a>
                <a href="#how-it-works" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">How It Works</a>
                <a href="#pricing" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Pricing</a>
                <a href="#faq" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">FAQ</a>
            </div>

            {{-- Auth Buttons --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="ff-btn-primary">
                        {{ __('Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                        {{ __('Log in') }}
                    </a>
                    <a href="{{ route('register') }}" class="ff-btn-primary">
                        {{ __('Get Started') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
