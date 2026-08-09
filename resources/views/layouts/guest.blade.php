<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1844D8">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <title>{{ config('app.name', 'FiberFlow') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=Manrope:wght@600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">

        {{-- Fiber console panel --}}
        <div class="ff-console ff-console-grid hidden lg:flex lg:w-[46%] xl:w-1/2 flex-col justify-between p-10 relative">
            <x-fiber-topology tone="dark" class="absolute inset-0 w-full h-full opacity-70" />
            <div class="ff-console-glow absolute inset-0 pointer-events-none"></div>

            {{-- Corner telemetry readouts --}}
            <div class="absolute top-8 right-10 hidden xl:block font-mono text-[10px] text-brand-300/50 tracking-widest text-right">
                <div>33.5731°N · 7.5898°W</div>
                <div>EPSG:3857 · Z16</div>
            </div>

            {{-- Wordmark --}}
            <div class="relative z-10 flex items-center gap-2.5">
                <x-application-logo class="w-8 h-8" />
                <span class="text-base font-semibold text-white tracking-tight">FiberFlow</span>
                <span class="ml-2 font-mono text-[10px] uppercase tracking-[0.22em] text-brand-300/60 border border-brand-300/20 rounded px-1.5 py-0.5">FTTH Engine</span>
            </div>

            {{-- Console readout --}}
            <div class="relative z-10 space-y-8">
                <div>
                    <p class="ff-eyebrow text-brand-300/70 mb-3 flex items-center gap-2">
                        <span class="ff-pulse-dot bg-emerald-400 text-emerald-400"></span>
                        Live network telemetry
                    </p>
                    <h1 class="text-4xl font-bold text-white tracking-tight leading-[1.15] max-w-md">
                        The audit instrument for <span class="text-gradient-brand">fiber networks.</span>
                    </h1>
                </div>

                {{-- Signal wave --}}
                <svg class="w-full max-w-md h-16" viewBox="0 0 480 64" fill="none" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="ff-wave" x1="0" y1="0" x2="480" y2="0" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#6B94FF" stop-opacity="0"/>
                            <stop offset="0.5" stop-color="#6B94FF" stop-opacity="0.9"/>
                            <stop offset="1" stop-color="#6B94FF" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0 40 Q 30 8 60 40 T 120 40 T 180 40 T 240 40 T 300 40 T 360 40 T 420 40 T 480 40"
                        stroke="url(#ff-wave)" stroke-width="1.5" class="animate-dash" stroke-dasharray="3 7"/>
                    <path d="M0 32 Q 40 20 80 32 T 160 32 T 240 32 T 320 32 T 400 32 T 480 32"
                        stroke="#3B6CFF" stroke-opacity="0.35" stroke-width="1"/>
                </svg>

                {{-- Readout card --}}
                <div class="ff-console-card relative p-5 max-w-md" x-data="consoleTelemetry">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3 mb-3">
                        <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-gray-400">Audit #69 · NRO71153CRI</span>
                        <span class="font-mono text-[10px] text-emerald-400 flex items-center gap-1.5">
                            <span class="ff-pulse-dot bg-emerald-400 text-emerald-400"></span>RUNNING
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 font-mono text-xs">
                        <div>
                            <div class="text-gray-500 mb-1">LAYERS</div>
                            <div class="text-white text-base">12</div>
                        </div>
                        <div>
                            <div class="text-gray-500 mb-1">SCORE</div>
                            <div class="text-brand-300 text-base" x-text="score.toFixed(1)">72.4</div>
                        </div>
                        <div>
                            <div class="text-gray-500 mb-1">CRITICAL</div>
                            <div class="text-amber-400 text-base">105</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between font-mono text-[10px] text-gray-500">
                        <span><span x-text="cables.toFixed(2)">10.81</span> km câble · 2 904 FO</span>
                        <span class="text-brand-300/70">▸ <span x-text="features.toLocaleString('fr-FR')">2 068</span> features</span>
                    </div>
                </div>
            </div>

            {{-- Footer line --}}
            <div class="relative z-10 font-mono text-[10px] uppercase tracking-[0.22em] text-gray-500">
                FTTH audit engine · quality · coherence · capacity
            </div>
        </div>

        {{-- Form side --}}
        <div class="w-full lg:w-[54%] xl:w-1/2 flex flex-col items-center justify-center bg-white px-6 py-12 relative">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-brand-600 via-brand-400 to-brand-600 opacity-20 pointer-events-none"></div>

            <div class="w-full max-w-sm">
                <div class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <x-application-logo class="w-8 h-8" />
                    <span class="text-lg font-semibold text-gray-900 tracking-tight">FiberFlow</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
