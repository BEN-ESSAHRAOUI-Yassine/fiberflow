<x-error-page
    code="404"
    title="{{ __('Page not found') }}"
    message="{{ __('The endpoint you requested does not exist or has been moved. Check the route, or return to the workspace.') }}"
    actionLabel="{{ __('Back to Dashboard') }}"
    actionUrl="{{ route('dashboard') }}"
/>
