@props(['tone' => 'dark', 'class' => ''])

@php
    $dark = $tone === 'dark';

    $routes = [
        'M220 300 L220 160 L360 160 L360 60',
        'M600 620 L760 620 L760 760 L900 760 L900 880',
        'M420 300 L420 120 L560 120 L560 40',
        'M820 240 L820 120 L940 120',
    ];

    $backbone = 'M60 800 L60 460 L220 460 L220 300 L420 300 L420 620 L600 620 L600 240 L820 240 L820 500 L1040 500 L1040 180';

    $splitters = [[220, 300], [420, 300], [600, 620], [820, 240], [1040, 500], [760, 620]];

    $nodes = [[60, 800], [60, 460], [220, 460], [420, 620], [600, 240], [820, 500], [1040, 180], [360, 60], [360, 160], [760, 760], [900, 760], [900, 880], [560, 40], [560, 120], [940, 120], [220, 160], [420, 120], [820, 120]];

    $stroke = $dark ? '#8FB3FF' : '#3B6CFF';
    $nodeFill = $dark ? '#D8E4FF' : '#1844D8';
@endphp

<svg class="{{ $class }}" viewBox="0 0 1200 900" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="ff-topo-mask-{{ $tone }}" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#fff"/>
            <stop offset="{{ $dark ? '0.4' : '0.62' }}" stop-color="#fff"/>
            <stop offset="1" stop-color="#fff" stop-opacity="0"/>
        </linearGradient>
        <mask id="ff-topo-fade-{{ $tone }}">
            <rect width="1200" height="900" fill="url(#ff-topo-mask-{{ $tone }})"/>
        </mask>
    </defs>

    <g mask="url(#ff-topo-fade-{{ $tone }})">
        @foreach ($routes as $route)
            <path d="{{ $route }}" stroke="{{ $stroke }}" stroke-opacity="{{ $dark ? 0.16 : 0.10 }}" stroke-width="1.2"/>
        @endforeach

        <path d="{{ $backbone }}" stroke="{{ $stroke }}" stroke-opacity="{{ $dark ? 0.45 : 0.3 }}" stroke-width="1.2" stroke-dasharray="3 12" class="ff-animate-dash-slow"/>

        @foreach ($splitters as [$x, $y])
            <circle cx="{{ $x }}" cy="{{ $y }}" r="10" stroke="{{ $stroke }}" stroke-opacity="{{ $dark ? 0.5 : 0.35 }}" stroke-width="1"/>
            <circle cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="{{ $nodeFill }}" fill-opacity="0.9"/>
        @endforeach

        @foreach ($nodes as [$x, $y])
            <circle cx="{{ $x }}" cy="{{ $y }}" r="2.6" fill="{{ $nodeFill }}" fill-opacity="0.75"/>
        @endforeach
    </g>
</svg>
