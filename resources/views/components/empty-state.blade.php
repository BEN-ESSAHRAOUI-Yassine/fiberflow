@props(['title' => null, 'description' => null])

<div class="ff-empty py-12">
    <div class="ff-empty-icon">
        @isset($icon)
            {{ $icon }}
        @else
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
            </svg>
        @endisset
    </div>
    @if ($title)
        <p class="text-sm font-medium text-gray-900">{{ $title }}</p>
    @endif
    @if ($description)
        <p class="text-sm text-gray-500 mt-1 max-w-sm">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
