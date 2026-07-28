@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @if($align === 'left')
         class="absolute z-50 mt-2 w-48 rounded-lg shadow-surface-lg ltr:origin-top-left rtl:origin-top-right start-0"
         @elseif($align === 'top')
         class="absolute z-50 mt-2 w-48 rounded-lg shadow-surface-lg origin-top"
         @else
         class="absolute z-50 mt-2 w-48 rounded-lg shadow-surface-lg ltr:origin-top-right rtl:origin-top-left end-0"
         @endif
         style="display: none;"
         @click="open = false">
        <div class="rounded-lg ring-1 ring-black/5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
