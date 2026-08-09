<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="__('Users')">
            <x-slot name="actions">
                <a href="{{ route('admin.users.create') }}" class="ff-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    {{ __('Create User') }}
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif

            <div class="ff-card">
                <div class="overflow-x-auto">
                    <table class="ff-table w-full">
                        <thead>
                            <tr>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-info-50 text-info-600 flex items-center justify-center text-sm font-semibold shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-brand-600 hover:text-brand-700 font-medium">{{ $user->name }}</a>
                                        </div>
                                    </td>
                                    <td class="text-gray-700">{{ $user->email }}</td>
                                    <td>
                                        <x-status-badge :status="$user->role->value" :dot="false">{{ $user->role->value }}</x-status-badge>
                                    </td>
                                    <td class="text-gray-500 text-sm">{{ $user->created_at->format('M j, Y') }}</td>
                                    <td class="text-right whitespace-nowrap">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="ff-btn-ghost text-sm">{{ __('Edit') }}</a>
                                        @if ($user->id !== auth()->id())
                                            <x-confirm-modal
                                                title="{{ __('Delete user?') }}"
                                                message="{{ __('This will permanently delete the user account.') }}"
                                                :action="route('admin.users.destroy', $user)"
                                                method="DELETE">
                                                <x-slot name="trigger">
                                                    <button type="button" class="ff-btn-ghost text-danger-600 hover:text-danger-700 text-sm">{{ __('Delete') }}</button>
                                                </x-slot>
                                                {{ __('Delete') }}
                                            </x-confirm-modal>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <x-empty-state :title="__('No users found')" :description="__('Create a user to get started.')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-surface-100">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
