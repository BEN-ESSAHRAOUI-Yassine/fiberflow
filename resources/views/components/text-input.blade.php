@props(['disabled' => false, 'leadingAddon' => false])

@if($leadingAddon)
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full rounded-lg border-0 px-3 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-500 sm:text-sm']) !!}>
@else
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'ff-input']) !!}>
@endif
