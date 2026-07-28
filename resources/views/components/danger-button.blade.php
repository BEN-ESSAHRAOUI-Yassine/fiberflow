@props(['disabled' => false])

<button {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['type' => 'submit', 'class' => 'ff-btn-danger']) }}>
    {{ $slot }}
</button>
