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
    <div class="min-h-screen flex">
        {{-- Branded Side --}}
        <div class="hidden lg:flex lg:w-1/2 bg-fiber-dark relative overflow-hidden items-center justify-center">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="relative z-10 text-center px-12">
                <div class="flex justify-center mb-6">
                    <x-application-logo />
                </div>
                <h1 class="text-3xl font-bold text-white mb-3">FiberFlow</h1>
                <p class="text-gray-400 text-sm leading-relaxed max-w-xs mx-auto">
                    {{ __('FTTH fiber audit platform for network quality analysis and compliance.') }}
                </p>
                <div class="mt-8 flex items-center justify-center gap-6 text-gray-500 text-xs">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Quality Audits') }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ __('AI Analysis') }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        {{ __('Reports') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Side --}}
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center bg-white px-6 py-12">
            <div class="w-full max-w-sm">
                <div class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <x-application-logo />
                    <span class="text-lg font-semibold text-gray-900">FiberFlow</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
