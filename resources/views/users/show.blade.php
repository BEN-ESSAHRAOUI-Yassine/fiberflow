<x-app-layout>
    <x-slot name="header">
        <h2 class="ff-page-title">{{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ff-card">
                <div class="p-6">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->name }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->email }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Role') }}</dt>
                            <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                @if($user->isAdmin())
                                    <span class="ff-badge-brand">{{ ucfirst($user->role->value) }}</span>
                                @else
                                    <span class="ff-badge-neutral">{{ ucfirst($user->role->value) }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Email Verified') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $user->email_verified_at ? $user->email_verified_at->format('M j, Y g:i A') : __('Not verified') }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Created') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->created_at->format('M j, Y g:i A') }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Updated') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->updated_at->format('M j, Y g:i A') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex items-center gap-4">
                        <a href="{{ route('admin.users.edit', $user) }}" class="ff-btn-primary">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="ff-btn-secondary">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
