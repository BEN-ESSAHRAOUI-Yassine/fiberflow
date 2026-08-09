<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1844D8">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <title>{{ config('app.name', 'FiberFlow') }}</title>
    <meta name="description" content="{{ __('The audit instrument for FTTH networks. Detect infrastructure anomalies, analyze optical networks, and generate intelligent audit reports.') }}">
    <meta property="og:title" content="{{ config('app.name', 'FiberFlow') }}">
    <meta property="og:description" content="{{ __('The audit instrument for FTTH networks. Detect anomalies, analyze fiber infrastructure, and generate intelligent audit reports.') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=JetBrains+Mono:wght@400;500;600&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/landing.js'])
</head>
<body class="font-sans antialiased bg-white">
    {{-- Navigation --}}
    <x-landing.nav />

    {{-- Hero --}}
    <x-landing.hero />

    {{-- Social Proof --}}
    <x-landing.social-proof />

    {{-- Features --}}
    <x-landing.features />

    {{-- How It Works --}}
    <x-landing.how-it-works />

    {{-- Product Preview --}}
    <x-landing.product-preview />

    {{-- Comparison --}}
    <x-landing.comparison />

    {{-- Pricing --}}
    <x-landing.pricing />

    {{-- Testimonials --}}
    <x-landing.testimonials />

    {{-- FAQ --}}
    <x-landing.faq />

    {{-- Final CTA --}}
    <x-landing.cta />

    {{-- Footer --}}
    <x-landing.footer />

    </body>
</html>
