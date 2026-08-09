<x-error-page
    code="419"
    title="{{ __('Session expired') }}"
    message="{{ __('Your session has expired. Sign in again to resume your work — your data is safe.') }}"
    actionLabel="{{ __('Sign in') }}"
    actionUrl="{{ route('login') }}"
/>
