<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :title="__('Audits')"
            :breadcrumbs="[['label' => $project->name, 'url' => route('admin.projects.show', $project)]]"
        >
            <x-slot name="meta">
                <span class="ff-pill">{{ $project->study_phase->value }}</span>
                <x-status-badge :status="$project->status->value" size="sm">{{ str_replace('_', ' ', $project->status->value) }}</x-status-badge>
            </x-slot>
            <x-slot name="actions">
                @can('create', App\Models\Audit::class)
                    @if ($project->datasets->isNotEmpty())
                        <form action="{{ route('admin.projects.audits.store', $project) }}" method="POST" class="inline" x-data="{ submitting: false }" x-on:submit="submitting = true">
                            @csrf
                            <button type="submit" class="ff-btn-primary" :disabled="submitting" :class="submitting ? 'opacity-60 cursor-wait pointer-events-none' : ''">
                                <svg x-cloak x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <svg x-cloak x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span x-text="submitting ? '{{ __('Auditing…') }}' : '{{ __('Run Audit') }}'">{{ __('Run Audit') }}</span>
                            </button>
                        </form>
                    @endif
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error">{{ session('error') }}</x-alert>
            @endif

            @if ($project->datasets->isEmpty())
                <div class="ff-card">
                    <x-empty-state
                        :title="__('No dataset imported yet')"
                        :description="__('Import a dataset before running an audit.')">
                        <x-slot name="icon">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2 3.5 4 8 4s8-2 8-4V7M4 7c0 2 3.5 4 8 4s8-2 8-4M4 7c0-2 3.5-4 8-4s8 2 8 4m0 5c0 2-3.5 4-8 4s-8-2-8-4"/></svg>
                        </x-slot>
                        @can('update', $project)
                            <a href="{{ route('admin.projects.datasets.import', $project) }}" class="ff-btn-primary">
                                {{ __('Import Dataset') }}
                            </a>
                        @endcan
                    </x-empty-state>
                </div>
            @elseif ($audits->isEmpty())
                <div class="ff-card">
                    <x-empty-state
                        :title="__('No audits performed yet')"
                        :description="__('Run an audit to check the quality of the fiber network.')">
                        <x-slot name="icon">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </x-slot>
                    </x-empty-state>
                </div>
            @else
                @if ($activeAudit)
                    <div x-data="auditWatcher('{{ route('admin.projects.audits.status', [$project, $activeAudit]) }}')">
                        <x-alert type="info">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                {{ __('Audit #') }}{{ $activeAudit->id }} {{ __('is running. This page will refresh automatically when it completes.') }}
                            </span>
                        </x-alert>
                    </div>
                @endif

                <div class="ff-card">
                    <div class="overflow-x-auto">
                        <table class="ff-table w-full">
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
                                            <x-status-badge :status="$audit->status->value">{{ ucfirst($audit->status->value) }}</x-status-badge>
                                        </td>
                                        <td>
                                            @if ($audit->quality_score !== null)
                                                <span class="font-semibold
                                                    @if ($audit->quality_score >= 90) ff-score-excellent
                                                    @elseif ($audit->quality_score >= 75) ff-score-good
                                                    @elseif ($audit->quality_score >= 50) ff-score-acceptable
                                                    @else ff-score-poor @endif
                                                ">{{ number_format($audit->quality_score, 1) }}</span>
                                            @else
                                                <span class="text-gray-400">--</span>
                                            @endif
                                        </td>
                                        <td class="text-right ff-data">
                                            {{ $audit->anomaly_count }}
                                            @if ($audit->critical_anomaly_count > 0)
                                                <span class="text-red-500 font-medium font-mono text-xs">/ {{ $audit->critical_anomaly_count }}</span>
                                            @endif
                                        </td>
                                        <td class="text-gray-500">{{ $audit->performer?->name ?? $audit->performed_by }}</td>
                                        <td class="text-right ff-data text-gray-500 font-mono text-xs">{{ $audit->completed_at?->format('M j, Y g:i A') ?? $audit->created_at->format('M j, Y g:i A') }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.projects.audits.show', [$project, $audit]) }}" class="ff-btn-ghost text-brand-600 hover:text-brand-700 text-sm font-medium">
                                                {{ __('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <x-empty-state :title="__('No audits found')" :description="__('No audits match the current view.')" />
                                        </td>
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
