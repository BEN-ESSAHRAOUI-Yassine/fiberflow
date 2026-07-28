@props(['name' => '', 'show' => false, 'maxWidth' => '2xl', 'focusable' => false])

<div
    x-data="{
        show: @js($show),
        focusable: @js($focusable),
        focusables() { this.focusable ?? this.$el.querySelectorAll('input, button, textarea, select, details, [tabindex]:not([tabindex=\'-1\'])') },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables()[this.focusables().length - 1] },
        nextFocusable() { return this.focusables()[this.nextElementIndex()] ?? this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevElementIndex()] ?? this.lastFocusable() },
        nextElementIndex() { return this.focusables().indexOf(document.activeElement) + 1 },
        prevElementIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
    }"
    x-init="$watch('show', value => {
        if (value) { document.body.classList.add('overflow-y-hidden'); }
        else { document.body.classList.remove('overflow-y-hidden'); }
    })"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey ? prevFocusable() : nextFocusable()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: {{ $show ? '' : 'none' }};"
>
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/50"></div>
    </div>

    <div x-show="show"
        @if($maxWidth === 'sm') class="mb-6 bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-sm sm:mx-auto"
        @elseif($maxWidth === 'md') class="mb-6 bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto"
        @elseif($maxWidth === 'lg') class="mb-6 bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
        @elseif($maxWidth === 'xl') class="mb-6 bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-xl sm:mx-auto"
        @else class="mb-6 bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto"
        @endif
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        {{ $slot }}
    </div>
</div>
