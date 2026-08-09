<x-error-page
    code="503"
    title="{{ __('Service unavailable') }}"
    message="{{ __('Maintenance window in progress. The audit engine will be back shortly — check back in a few minutes.') }}"
    actionLabel="{{ __('Reload') }}"
    actionUrl="{{ request()->url() }}"
/>
