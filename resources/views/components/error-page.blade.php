@props(['code', 'title', 'message', 'actionLabel' => null, 'actionUrl' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#1844D8">
    <title>{{ $code }} &middot; {{ config('app.name', 'FiberFlow') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=Manrope:wght@600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased">
    <div class="ff-console ff-console-grid min-h-screen flex flex-col justify-between p-10 relative">
        <x-fiber-topology tone="dark" class="absolute inset-0 w-full h-full opacity-70" />
        <div class="ff-console-glow absolute inset-0 pointer-events-none"></div>

        <div class="absolute top-8 right-10 hidden xl:block font-mono text-[10px] text-brand-300/50 tracking-widest text-right">
            <div>ERR-{{ $code }}</div>
            <div>FTTH AUDIT ENGINE</div>
        </div>

        <div class="relative z-10 flex items-center gap-2.5">
            <x-application-logo class="w-8 h-8" />
            <span class="text-base font-semibold text-white tracking-tight">FiberFlow</span>
            <span class="ml-2 font-mono text-[10px] uppercase tracking-[0.22em] text-brand-300/60 border border-brand-300/20 rounded px-1.5 py-0.5">FTTH Engine</span>
        </div>

        <div class="relative z-10 flex items-center justify-center">
            <div class="text-center max-w-lg">
                <p class="ff-eyebrow text-brand-300/70 mb-4 font-mono">// {{ $code }}_exception_thrown</p>
                <div class="font-mono text-[7rem] leading-none font-semibold text-white tracking-tight">
                    {{ $code }}
                    <span class="ff-pulse-dot bg-emerald-400 text-emerald-400 align-middle"></span>
                </div>
                <h1 class="mt-4 text-2xl font-semibold text-white">{{ $title }}</h1>
                <p class="mt-3 text-sm text-gray-400 leading-relaxed max-w-md mx-auto">{{ $message }}</p>

                <div class="mt-8 flex items-center justify-center gap-3">
                    @if ($actionUrl)
                        <a href="{{ $actionUrl }}" class="ff-btn-primary">
                            {{ $actionLabel }}
                        </a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="ff-btn-ghost text-white/70 hover:text-white">{{ __('Go to Dashboard') }}</a>
                </div>
            </div>
        </div>

        <div class="relative z-10 font-mono text-[10px] uppercase tracking-[0.22em] text-gray-500">
            fiberflow &middot; network audit console
        </div>
    </div>
</body>
</html>
