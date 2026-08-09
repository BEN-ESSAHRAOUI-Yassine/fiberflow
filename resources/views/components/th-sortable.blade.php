@props(['name', 'label', 'align' => 'left'])

@php
    $current = request('sort');
    $direction = request('direction');
    $active = $current === $name;
    $nextDir = $active && $direction === 'asc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $name, 'direction' => $nextDir]);
@endphp

<th class="px-4 py-3 {{ $align === 'right' ? 'text-right' : 'text-left' }}">
    <a href="{{ $url }}" class="inline-flex items-center gap-1 {{ $active ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
        {{ $label }}
        @if ($active)
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if ($direction === 'asc')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                @endif
            </svg>
        @else
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l5-5 5 5M7 14l5 5 5-5" />
            </svg>
        @endif
    </a>
</th>
