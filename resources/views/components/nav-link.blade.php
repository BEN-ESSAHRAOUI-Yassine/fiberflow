@props(['active' => false])

<a {{ $attributes->merge(['class' => $active
    ? 'inline-flex items-center px-3 py-1 text-sm font-medium text-brand-700 border-b-2 border-brand-700 focus:outline-none transition duration-150 ease-in-out'
    : 'inline-flex items-center px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent focus:outline-none focus:text-gray-700 transition duration-150 ease-in-out'
]) }}>
    {{ $slot }}
</a>
