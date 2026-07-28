<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="ff-page-title">{{ __('Audits') }}: {{ $project->name }}</h2>
            @can('create', App\Models\Audit::class)
                @if ($project->datasets->isNotEmpty())
                    <form action="{{ route('admin.projects.audits.store', $project) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="ff-btn-primary">
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
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="ff-card">
                <div class="p-6">
                    @if ($project->datasets->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No dataset imported yet. Import a dataset before running an audit.') }}</p>
                    @elseif ($audits->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No audits performed yet.') }}</p>
                    @else
                        <table class="ff-table">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('ID') }}</th>
                                    <th class="text-left">{{ __('Status') }}</th>
                                    <th class="text-left">{{ __('Score') }}</th>
                                    <th class="text-left">{{ __('Anomalies') }}</th>
                                    <th class="text-left">{{ __('Performer') }}</th>
                                    <th class="text-left">{{ __('Date') }}</th>
                                    <th class="text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($audits as $audit)
                                    <tr>
                                        <td class="text-gray-900">#{{ $audit->id }}</td>
                                        <td>
                                            <span class="ff-badge
                                                @switch($audit->status->value)
                                                    @case('pending') ff-badge-warning @break
                                                    @case('running') ff-badge-brand @break
                                                    @case('completed') ff-badge-success @break
                                                    @case('failed') ff-badge-danger @break
                                                @endswitch
                                            ">
                                                <span class="ff-dot
                                                    @switch($audit->status->value)
                                                        @case('pending') ff-dot-warning @break
                                                        @case('running') ff-dot-info @break
                                                        @case('completed') ff-dot-success @break
                                                        @case('failed') ff-dot-danger @break
                                                    @endswitch
                                                "></span>
                                                {{ ucfirst($audit->status->value) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($audit->quality_score !== null)
                                                <span class="ff-badge
                                                    @if ($audit->quality_score >= 90) ff-badge-success
                                                    @elseif ($audit->quality_score >= 75) ff-badge-brand
                                                    @elseif ($audit->quality_score >= 50) ff-badge-warning
                                                    @else ff-badge-danger @endif
                                                ">
                                                    {{ number_format($audit->quality_score, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">--</span>
                                            @endif
                                        </td>
                                        <td class="text-gray-500">
                                            @if ($audit->anomaly_count > 0 || $audit->critical_anomaly_count > 0)
                                                {{ $audit->anomaly_count }} / {{ $audit->critical_anomaly_count }}
                                                <span class="text-xs text-gray-400">(normal/critical)</span>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="text-gray-500">{{ $audit->performer?->name ?? $audit->performed_by }}</td>
                                        <td class="text-gray-500">
                                            {{ $audit->completed_at?->format('M j, Y g:i A') ?? $audit->created_at->format('M j, Y g:i A') }}
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.projects.audits.show', [$project, $audit]) }}" class="text-brand-600 hover:text-brand-700 font-medium text-sm">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-gray-500">{{ __('No audits found.') }}</td>
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
