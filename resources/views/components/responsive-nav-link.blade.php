@props(['active' => false])

<a {{ $attributes->merge(['class' => $active
    ? 'block w-full ps-3 pe-4 py-2 border-l-2 border-brand-700 text-start text-base font-medium text-brand-700 bg-brand-50 focus:outline-none transition duration-150 ease-in-out'
    : 'block w-full ps-3 pe-4 py-2 border-l-2 border-transparent text-start text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-surface-50 focus:outline-none transition duration-150 ease-in-out'
]) }}>
    {{ $slot }}
</a>
