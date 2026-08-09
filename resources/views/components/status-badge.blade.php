@props(['status' => null, 'dot' => true, 'size' => 'sm', 'title' => null])

@php
    $map = [
        'draft' => ['text-gray-600 bg-gray-100', 'bg-gray-400'],
        'in_progress' => ['text-warning-700 bg-warning-50', 'bg-warning-500'],
        'audited' => ['text-info-700 bg-info-50', 'bg-info-500'],
        'validated' => ['text-success-700 bg-success-50', 'bg-success-500'],
        'archived' => ['text-gray-600 bg-gray-100', 'bg-gray-400'],
        'pending' => ['text-warning-700 bg-warning-50', 'bg-warning-500'],
        'running' => ['text-info-700 bg-info-50', 'bg-info-500'],
        'completed' => ['text-success-700 bg-success-50', 'bg-success-500'],
        'failed' => ['text-danger-700 bg-danger-50', 'bg-danger-500'],
        'admin' => ['text-info-700 bg-info-50', 'bg-info-500'],
        'ingenieur' => ['text-gray-600 bg-gray-100', 'bg-gray-400'],
    ];

    $classes = $map[$status] ?? ['text-gray-600 bg-gray-100', 'bg-gray-400'];
@endphp

<span title="{{ $title }}" class="inline-flex items-center gap-1.5 rounded-md font-medium
    @if ($size === 'lg') px-3 py-1 text-sm rounded-full
    @else px-2.5 py-0.5 text-xs @endif
    {{ $classes[0] }}">
    @if ($dot)
        <span class="inline-block w-2 h-2 rounded-full {{ $classes[1] }}"></span>
    @endif
    {{ $slot }}
</span>
