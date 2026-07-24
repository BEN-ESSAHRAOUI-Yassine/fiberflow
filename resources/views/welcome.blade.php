<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FiberFlow') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-indigo-950">
        <header class="px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <x-application-logo class="w-8 h-8 text-indigo-400" />
                    <span class="text-xl font-bold text-white">FiberFlow</span>
                </div>

                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition text-sm font-medium">{{ __('Log in') }}</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Register') }}
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto px-6 py-20 lg:py-32">
                <div class="text-center max-w-3xl mx-auto">
                    <x-application-logo class="w-16 h-16 mx-auto text-indigo-400 mb-6" />
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 tracking-tight">FiberFlow</h1>
                    <p class="text-lg lg:text-xl text-gray-400 mb-10 leading-relaxed">
                        {{ __('Streamline your fiber network projects from design to delivery.') }}
                    </p>

                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition ease-in-out duration-150">
                            {{ __('Go to Dashboard') }}
                        </a>
                    @else
                        <div class="flex items-center justify-center gap-4">
                            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition ease-in-out duration-150">
                                {{ __('Log in') }}
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-gray-600 rounded-lg font-semibold text-sm text-gray-300 uppercase tracking-widest hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition ease-in-out duration-150">
                                {{ __('Get Started') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 pb-20 lg:pb-32">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
                        <div class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">{{ __('Project Management') }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ __('Manage transport and distribution fiber projects with full lifecycle tracking.') }}</p>
                    </div>

                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
                        <div class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">{{ __('Phase Tracking') }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ __('Track projects through APS, APD, PRO, and EXE phases with structured workflows.') }}</p>
                    </div>

                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
                        <div class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">{{ __('Team Collaboration') }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ __('Assign roles, manage permissions, and keep your team aligned every step of the way.') }}</p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="border-t border-white/10 py-6">
            <div class="max-w-7xl mx-auto px-6 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name', 'FiberFlow') }}. {{ __('All rights reserved.') }}
            </div>
        </footer>
    </div>
</body>
</html>
