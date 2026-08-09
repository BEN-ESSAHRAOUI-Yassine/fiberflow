@props(['type' => 'success'])

@php
    $styles = [
        'success' => ['bg-success-50 border-success-200 text-success-700', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        'error' => ['bg-danger-50 border-danger-200 text-danger-700', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        'info' => ['bg-info-50 border-info-200 text-info-700', 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'warning' => ['bg-warning-50 border-warning-200 text-warning-700', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
    ];
@endphp

<div class="p-4 border rounded-lg text-sm flex items-start gap-2.5 {{ $styles[$type][0] }}" role="alert">
    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $styles[$type][1] }}" />
    </svg>
    <div class="min-w-0">{{ $slot }}</div>
</div>
