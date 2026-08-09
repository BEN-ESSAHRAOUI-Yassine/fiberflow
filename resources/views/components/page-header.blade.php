@props(['title', 'breadcrumbs' => []])

<div class="ff-page-header-actions">
    <div>
        @if (count($breadcrumbs))
            <div class="ff-breadcrumb">
                @foreach ($breadcrumbs as $index => $crumb)
                    @if (isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                        @if ($index < count($breadcrumbs) - 1)
                            <span class="ff-breadcrumb-sep font-mono">/</span>
                        @endif
                    @else
                        <span class="text-gray-900">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </div>
        @endif
        <h1 class="ff-page-title text-2xl tracking-tight">{{ $title }}</h1>
        @isset($meta)
            <div class="ff-pills mt-2">{{ $meta }}</div>
        @endisset
    </div>
    @isset($actions)
        <div class="flex items-center gap-3">{{ $actions }}</div>
    @endisset
</div>
