@props(['label', 'value', 'iconColor' => 'brand'])

@php
    $iconColors = [
        'brand' => 'bg-info-50 text-info-600',
        'success' => 'bg-success-50 text-success-600',
        'warning' => 'bg-warning-50 text-warning-600',
        'danger' => 'bg-danger-50 text-danger-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'sky' => 'bg-sky-50 text-sky-600',
    ];
@endphp

<div class="ff-stat-card" {{ $attributes }}>
    @isset($icon)
        <div class="flex items-center justify-between">
            <div class="ff-stat-card-icon {{ $iconColors[$iconColor] ?? $iconColors['brand'] }}">
                {{ $icon }}
            </div>
        </div>
    @endisset
    <div class="ff-stat-card-value mt-3">{{ $value }}</div>
    <div class="ff-stat-card-label">
        {{ $label }}
        @isset($sub)
            <span class="text-red-600 font-mono">{{ $sub }}</span>
        @endisset
    </div>
</div>
