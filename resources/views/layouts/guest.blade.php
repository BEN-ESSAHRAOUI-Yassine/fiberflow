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
    <div class="min-h-screen flex flex-col items-center justify-center bg-surface-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="/" class="flex items-center gap-2.5">
                <x-application-logo />
                <span class="text-lg font-semibold text-gray-900">FiberFlow</span>
            </a>
        </div>

        <div class="w-full max-w-sm">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
