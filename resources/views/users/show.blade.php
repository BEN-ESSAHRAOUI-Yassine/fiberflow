<x-app-layout>
    <x-slot name="header">
        <div class="ff-page-header-actions">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center text-lg font-bold shrink-0">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="ff-breadcrumb">
                        <a href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
                        <span class="ff-breadcrumb-sep">/</span>
                        <span class="text-gray-900">{{ $user->name }}</span>
                    </div>
                    <div class="flex items-center gap-3 mt-1">
                        <h1 class="ff-page-title text-2xl">{{ $user->name }}</h1>
                        @if($user->isAdmin())
                            <span class="ff-badge-lg bg-brand-50 text-brand-700">{{ $user->role->value }}</span>
                        @else
                            <span class="ff-badge-lg bg-gray-100 text-gray-600">{{ $user->role->value }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="ff-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('admin.users.index') }}" class="ff-btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 ff-card">
                    <div class="p-6">
                        <h3 class="ff-section-header mb-4">{{ __('Profile Information') }}</h3>
                        <dl class="divide-y divide-surface-100">
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('Name') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2">{{ $user->name }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('Email') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2">{{ $user->email }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('Role') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2">
                                    @if($user->isAdmin())
                                        <span class="ff-badge-brand">{{ ucfirst($user->role->value) }}</span>
                                    @else
                                        <span class="ff-badge-neutral">{{ ucfirst($user->role->value) }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="ff-dl-label">{{ __('Email Verified') }}</dt>
                                <dd class="ff-dl-value sm:col-span-2">
                                    @if ($user->email_verified_at)
                                        <span class="ff-badge-success">{{ $user->email_verified_at->format('M j, Y g:i A') }}</span>
                                    @else
                                        <span class="ff-badge-warning">{{ __('Not verified') }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="ff-card">
                        <div class="p-6">
                            <h3 class="ff-section-header mb-4">{{ __('Activity') }}</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="ff-dl-label">{{ __('Created') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $user->created_at->format('M j, Y g:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="ff-dl-label">{{ __('Last Updated') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $user->updated_at->format('M j, Y g:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="ff-dl-label">{{ __('Audits Performed') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $user->audits()->count() }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
