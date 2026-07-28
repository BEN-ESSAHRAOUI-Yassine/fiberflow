<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="ff-page-title">{{ __('Users') }}</h2>
            <a href="{{ route('admin.users.create') }}" class="ff-btn-primary">
                {{ __('Create User') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="ff-card">
                <div class="p-6">
                    <table class="ff-table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="font-medium">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-brand-600 hover:text-brand-700">{{ $user->name }}</a>
                                    </td>
                                    <td class="text-gray-700">{{ $user->email }}</td>
                                    <td>
                                        @if($user->isAdmin())
                                            <span class="ff-badge-brand">{{ $user->role->value }}</span>
                                        @else
                                            <span class="ff-badge-neutral">{{ $user->role->value }}</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-500">{{ $user->created_at->format('M j, Y') }}</td>
                                    <td class="text-right font-medium space-x-3">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-brand-600 hover:text-brand-700">{{ __('Edit') }}</a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('{{ __('Delete this user?') }}')">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500">{{ __('No users found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
