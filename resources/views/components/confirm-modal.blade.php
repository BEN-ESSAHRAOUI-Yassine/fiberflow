@props(['title', 'message' => null, 'action', 'method' => 'DELETE'])

<div x-data="{ open: false }" {{ $attributes }}>
    <span @click="open = true" class="inline-flex">{{ $trigger }}</span>

    <div x-show="open" x-cloak
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" @click="open = false"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-gray-900/50"></div>

        <div x-show="open" @click.away="open = false"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="relative w-full max-w-md bg-white rounded-xl shadow-surface-lg p-6" role="dialog" aria-modal="true" aria-label="{{ $title }}">
            <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
            @if ($message)
                <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>
            @endif

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" @click="open = false" class="ff-btn-secondary">{{ __('Cancel') }}</button>
                <form method="POST" action="{{ $action }}" class="inline">
                    @csrf
                    @method($method)
                    <button type="submit" class="ff-btn-danger">{{ $slot }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
