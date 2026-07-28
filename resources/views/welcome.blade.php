<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FiberFlow') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .fiber-grid {
            background-image:
                linear-gradient(rgba(59, 108, 255, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 108, 255, 0.06) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .fiber-glow {
            background: radial-gradient(ellipse 60% 50% at 50% 40%, rgba(24, 68, 216, 0.15), transparent);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-fiber-dark text-white">
        {{-- Nav --}}
        <header class="relative z-10 px-6 py-4">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <a href="/" class="flex items-center gap-2.5">
                    <x-application-logo class="w-8 h-8" />
                    <span class="text-base font-semibold text-white">FiberFlow</span>
                </a>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="ff-btn-primary">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">{{ __('Log in') }}</a>
                        <a href="{{ route('register') }}" class="ff-btn-primary">
                            {{ __('Get started') }}
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        {{-- Hero --}}
        <main class="relative">
            <div class="fiber-grid absolute inset-0"></div>
            <div class="fiber-glow absolute inset-0"></div>

            <div class="relative max-w-6xl mx-auto px-6 pt-24 pb-32 lg:pt-36 lg:pb-44">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-medium text-gray-400 mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('AI-powered fiber network auditing') }}
                    </div>

                    <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight tracking-tight mb-5">
                        {{ __('FTTH audit intelligence, from design to delivery.') }}
                    </h1>

                    <p class="text-lg text-gray-400 leading-relaxed mb-10 max-w-lg">
                        {{ __('Streamline transport and distribution fiber projects with structured audits, AI analysis, and quality scoring.') }}
                    </p>

                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="ff-btn-primary px-6 py-3 text-base">
                                {{ __('Go to Dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="ff-btn-primary px-6 py-3 text-base">
                                {{ __('Get started') }}
                            </a>
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-400 hover:text-white transition-colors px-4 py-3">
                                {{ __('Sign in') }}
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Subtle network visualization --}}
                <div class="hidden lg:block absolute right-0 top-1/2 -translate-y-1/2 w-96 h-96 opacity-20">
                    <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="200" cy="200" r="150" stroke="#3B6CFF" stroke-width="0.5" opacity="0.3"/>
                        <circle cx="200" cy="200" r="100" stroke="#3B6CFF" stroke-width="0.5" opacity="0.4"/>
                        <circle cx="200" cy="200" r="50" stroke="#3B6CFF" stroke-width="0.5" opacity="0.5"/>
                        <line x1="200" y1="50" x2="200" y2="350" stroke="#3B6CFF" stroke-width="0.5" opacity="0.2"/>
                        <line x1="50" y1="200" x2="350" y2="200" stroke="#3B6CFF" stroke-width="0.5" opacity="0.2"/>
                        <line x1="94" y1="94" x2="306" y2="306" stroke="#3B6CFF" stroke-width="0.5" opacity="0.2"/>
                        <line x1="306" y1="94" x2="94" y2="306" stroke="#3B6CFF" stroke-width="0.5" opacity="0.2"/>
                        <circle cx="200" cy="50" r="4" fill="#3B6CFF"/>
                        <circle cx="350" cy="200" r="4" fill="#3B6CFF"/>
                        <circle cx="200" cy="350" r="4" fill="#3B6CFF"/>
                        <circle cx="50" cy="200" r="4" fill="#3B6CFF"/>
                        <circle cx="306" cy="94" r="3" fill="#3B6CFF" opacity="0.6"/>
                        <circle cx="94" cy="306" r="3" fill="#3B6CFF" opacity="0.6"/>
                        <circle cx="306" cy="306" r="3" fill="#3B6CFF" opacity="0.6"/>
                        <circle cx="94" cy="94" r="3" fill="#3B6CFF" opacity="0.6"/>
                        <circle cx="200" cy="200" r="6" fill="white"/>
                    </svg>
                </div>
            </div>

            {{-- Stats bar --}}
            <div class="relative border-t border-white/5">
                <div class="max-w-6xl mx-auto px-6 py-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div>
                            <div class="text-2xl font-bold text-white">2</div>
                            <div class="text-sm text-gray-500 mt-1">{{ __('Active projects') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">47</div>
                            <div class="text-sm text-gray-500 mt-1">{{ __('Audits completed') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">19K+</div>
                            <div class="text-sm text-gray-500 mt-1">{{ __('Anomalies detected') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">94.3</div>
                            <div class="text-sm text-gray-500 mt-1">{{ __('Avg quality score') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="relative border-t border-white/5 py-8">
            <div class="max-w-6xl mx-auto px-6 flex items-center justify-between text-sm text-gray-600">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'FiberFlow') }}</span>
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ __('System operational') }}
                </span>
            </div>
        </footer>
    </div>
</body>
</html>
