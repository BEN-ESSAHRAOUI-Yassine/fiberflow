<x-app-layout>
    <x-slot name="header">
        <div class="ff-page-header-actions">
            <div>
                <div class="ff-breadcrumb">
                    <a href="{{ route('admin.projects.show', $project) }}">{{ $project->name }}</a>
                    <span class="ff-breadcrumb-sep">/</span>
                    <span class="text-gray-900">{{ __('Audits') }}</span>
                </div>
                <h1 class="ff-page-title text-2xl">{{ __('Audits') }}</h1>
            </div>
            @can('create', App\Models\Audit::class)
                @if ($project->datasets->isNotEmpty())
                    <form action="{{ route('admin.projects.audits.store', $project) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="ff-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('Run Audit') }}
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if ($project->datasets->isEmpty())
                <div class="ff-card">
                    <div class="ff-empty py-12">
                        <div class="ff-empty-icon">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2 3.5 4 8 4s8-2 8-4V7M4 7c0 2 3.5 4 8 4s8-2 8-4M4 7c0-2 3.5-4 8-4s8 2 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                        </div>
                        <p class="text-sm text-gray-500 mb-3">{{ __('No dataset imported yet. Import a dataset before running an audit.') }}</p>
                        <a href="{{ route('admin.projects.datasets.import', $project) }}" class="ff-btn-primary">
                            {{ __('Import Dataset') }}
                        </a>
                    </div>
                </div>
            @elseif ($audits->isEmpty())
                <div class="ff-card">
                    <div class="ff-empty py-12">
                        <div class="ff-empty-icon">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <p class="text-sm text-gray-500">{{ __('No audits performed yet.') }}</p>
                    </div>
                </div>
            @else
                <div class="ff-card">
                    <div class="overflow-x-auto">
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
                                        <td class="font-mono text-gray-500">#{{ $audit->id }}</td>
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
                                        <td class="text-gray-500 text-sm">
                                            {{ $audit->anomaly_count }}
                                            @if ($audit->critical_anomaly_count > 0)
                                                <span class="text-red-500">/ {{ $audit->critical_anomaly_count }} {{ __('crit') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-gray-500">{{ $audit->performer?->name ?? $audit->performed_by }}</td>
                                        <td class="text-gray-500 text-sm">{{ $audit->completed_at?->format('M j, Y g:i A') ?? $audit->created_at->format('M j, Y g:i A') }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.projects.audits.show', [$project, $audit]) }}" class="ff-btn-ghost text-brand-600 hover:text-brand-700 text-sm font-medium">
                                                {{ __('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-gray-500 py-8">{{ __('No audits found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 border-t border-surface-100">
                        {{ $audits->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
