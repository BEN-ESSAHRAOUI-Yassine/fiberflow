@props(['value' => null, 'required' => false])

<label {{ $attributes->merge(['class' => 'ff-label']) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <span class="text-red-500">*</span>
    @endif
</label>
