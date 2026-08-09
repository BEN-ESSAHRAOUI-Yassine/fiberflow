<x-error-page
    code="403"
    title="{{ __('Forbidden') }}"
    message="{{ __('Your account does not have clearance for this resource. Ask an administrator if you believe this is a mistake.') }}"
    actionLabel="{{ __('Back to Dashboard') }}"
    actionUrl="{{ route('dashboard') }}"
/>
