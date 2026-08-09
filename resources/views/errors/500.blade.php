<x-error-page
    code="500"
    title="{{ __('Server error') }}"
    message="{{ __('Something went wrong while processing your request. The incident has been logged — try again, or contact support.') }}"
    actionLabel="{{ __('Retry') }}"
    actionUrl="{{ request()->url() }}"
/>
