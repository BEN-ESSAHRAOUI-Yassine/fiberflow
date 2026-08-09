@props(['value' => 0, 'max' => 100, 'size' => 160, 'label' => null, 'sub' => null, 'target' => 85])

@php
    $pct = min(100, max(0, ($value / max(1, $max)) * 100));
    $color = $value >= 90 ? '#10B981' : ($value >= 75 ? '#2456F5' : ($value >= 50 ? '#F59E0B' : '#DC2626'));
    $r = ($size / 2) - 11;
    $circ = 2 * 3.14159265 * $r;
    $dash = max(0, $circ * $pct / 100);
    $cx = $size / 2;
    $targetAngle = (($target / max(1, $max)) * 360 - 90) * M_PI / 180;
    $tx1 = $cx + ($r - 8) * cos($targetAngle);
    $ty1 = $cx + ($r - 8) * sin($targetAngle);
    $tx2 = $cx + ($r + 8) * cos($targetAngle);
    $ty2 = $cx + ($r + 8) * sin($targetAngle);
@endphp

<div class="ff-gauge" style="width: {{ $size }}px; height: {{ $size }}px;" role="img" aria-label="{{ $label ? $label.': '.$value : 'Score: '.$value }}">
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}" fill="none" aria-hidden="true">
        <circle cx="{{ $cx }}" cy="{{ $cx }}" r="{{ $r }}" stroke="#F3F4F6" stroke-width="10" />
        @if ($target !== null)
            <line x1="{{ $tx1 }}" y1="{{ $ty1 }}" x2="{{ $tx2 }}" y2="{{ $ty2 }}" stroke="#9CA3AF" stroke-width="2" />
        @endif
        <circle cx="{{ $cx }}" cy="{{ $cx }}" r="{{ $r }}" stroke="{{ $color }}" stroke-width="10"
            stroke-linecap="round"
            stroke-dasharray="{{ $dash }} {{ $circ }}"
            transform="rotate(-90 {{ $cx }} {{ $cx }})"
            class="transition-all duration-700 ease-out" />
    </svg>
    <div class="absolute inset-0 flex flex-col items-center justify-center">
        <span class="font-mono text-3xl font-bold tracking-tight" style="color: {{ $color }}">{{ $value }}</span>
        @if ($label)
            <span class="text-[10px] uppercase tracking-[0.18em] text-gray-400 mt-0.5">{{ $label }}</span>
        @endif
        @if ($sub)
            <span class="text-xs text-gray-500 mt-0.5">{{ $sub }}</span>
        @endif
    </div>
</div>
