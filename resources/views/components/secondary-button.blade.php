@props(['disabled' => false])

<button {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['type' => 'button', 'class' => 'ff-btn-secondary']) }}>
    {{ $slot }}
</button>
