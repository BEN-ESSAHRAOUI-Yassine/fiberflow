<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Audits') }}: {{ $project->name }}</h2>
            @can('create', App\Models\Audit::class)
                @if ($project->datasets->isNotEmpty())
                    <form action="{{ route('admin.projects.audits.store', $project) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Run Audit') }}
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($project->datasets->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No dataset imported yet. Import a dataset before running an audit.') }}</p>
                    @elseif ($audits->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No audits performed yet.') }}</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Score') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Anomalies') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Performer') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($audits as $audit)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $audit->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @switch($audit->status->value)
                                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                                    @case('running') bg-blue-100 text-blue-800 @break
                                                    @case('completed') bg-green-100 text-green-800 @break
                                                    @case('failed') bg-red-100 text-red-800 @break
                                                @endswitch
                                            ">
                                                {{ ucfirst($audit->status->value) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($audit->quality_score !== null)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if ($audit->quality_score >= 90) bg-green-100 text-green-800
                                                    @elseif ($audit->quality_score >= 75) bg-blue-100 text-blue-800
                                                    @elseif ($audit->quality_score >= 50) bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif
                                                ">
                                                    {{ number_format($audit->quality_score, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">--</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($audit->anomaly_count > 0 || $audit->critical_anomaly_count > 0)
                                                {{ $audit->anomaly_count }} / {{ $audit->critical_anomaly_count }}
                                                <span class="text-xs text-gray-400">(normal/critical)</span>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $audit->performer?->name ?? $audit->performed_by }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $audit->completed_at?->format('M j, Y g:i A') ?? $audit->created_at->format('M j, Y g:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.projects.audits.show', [$project, $audit]) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No audits found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $audits->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
