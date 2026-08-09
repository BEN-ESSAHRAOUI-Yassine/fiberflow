<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="__('Dashboard')">
            <x-slot name="meta">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <p class="text-sm text-gray-500">{{ __('Welcome back,') }} <span class="font-medium text-gray-900">{{ Auth::user()->name }}</span></p>
                    <p class="ff-data font-mono text-[10px] uppercase tracking-[0.16em] text-gray-400">
                        <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>db:ok</span>
                        &middot; audits:{{ $data['audits_count'] }}
                    </p>
                </div>
            </x-slot>
            <x-slot name="actions">
                @can('create', App\Models\Project::class)
                    <a href="{{ route('admin.projects.create') }}" class="ff-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Create Project') }}
                    </a>
                @endcan
                @can('viewAny', App\Models\User::class)
                    <a href="{{ route('admin.users.index') }}" class="ff-btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
                        {{ __('Manage Users') }}
                    </a>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Stat Cards + Quality gauge --}}
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-4">
                <div class="xl:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-stat-card :label="__('Projects')" :value="$data['projects_count']">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                        </x-slot>
                    </x-stat-card>

                    <x-stat-card :label="__('Audits')" :value="$data['audits_count']">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </x-slot>
                    </x-stat-card>

                    <x-stat-card :label="__('Anomalies')" :value="number_format($data['total_anomalies'])" :sub="$data['total_critical_anomalies'] > 0 ? $data['total_critical_anomalies'].' '.__('critical') : null" :icon-color="$data['total_critical_anomalies'] > 0 ? 'danger' : 'success'">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </x-slot>
                    </x-stat-card>
                </div>

                <div class="ff-card p-4 flex items-center justify-center">
                    <x-gauge :value="$data['average_quality_score']" size="148" :label="__('Avg Quality')" />
                </div>
            </div>

            {{-- Pending audits alert (engineers focus) --}}
            @if (($data['audits_by_status']['pending'] ?? 0) > 0)
                <x-alert type="warning">
                    {{ __('There are') }} <strong>{{ $data['audits_by_status']['pending'] }}</strong> {{ __('audit(s) waiting to be processed.') }}
                </x-alert>
            @endif

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="ff-card p-4">
                    <h3 class="ff-section-header mb-3">{{ __('Projects by Type') }}</h3>
                    <div style="height: 200px;">
                        <canvas id="chartProjectType"></canvas>
                    </div>
                </div>

                <div class="ff-card p-4">
                    <h3 class="ff-section-header mb-3">{{ __('Projects by Status') }}</h3>
                    <div style="height: 200px;">
                        <canvas id="chartProjectStatus"></canvas>
                    </div>
                </div>

                <div class="ff-card p-4">
                    <h3 class="ff-section-header mb-3">{{ __('Audits by Status') }}</h3>
                    <div style="height: 200px;">
                        <canvas id="chartAuditStatus"></canvas>
                    </div>
                </div>
            </div>

            {{-- Recent Audits --}}
            <div class="ff-card overflow-hidden">
                <div class="p-4 border-b border-surface-100 flex items-center justify-between">
                    <h3 class="ff-section-header">{{ __('Recent Audits') }}</h3>
                    <div class="flex items-center gap-3">
                        @if (! Auth::user()->isAdmin())
                            <span class="text-xs text-gray-400">{{ __('Showing audits you performed') }}</span>
                        @endif
                        <a href="{{ route('admin.projects.index') }}" class="ff-btn-ghost text-xs">{{ __('View all') }} &rarr;</a>
                    </div>
                </div>
                @if (empty($data['recent_audits']))
                    <x-empty-state :title="__('No audits yet')" :description="__('Audits appear here once you run them on a project.')">
                        <x-slot name="icon">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </x-slot>
                    </x-empty-state>
                @else
                    <div class="overflow-x-auto">
                        <table class="ff-table w-full">
                            <thead>
                                <tr>
                                    <th class="w-10">#</th>
                                    <th>{{ __('Project') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-right">{{ __('Score') }}</th>
                                    <th class="text-right">{{ __('Anomalies') }}</th>
                                    <th>{{ __('Performer') }}</th>
                                    <th class="text-right">{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['recent_audits'] as $audit)
                                <tr>
                                    <td class="ff-data font-mono text-xs text-gray-400">#{{ $audit['id'] }}</td>
                                    <td>
                                        <a href="{{ route('admin.projects.audits.show', [$audit['project_id'], $audit['id']]) }}" class="font-medium text-brand-600 hover:text-brand-700">
                                            {{ $audit['project_name'] }}
                                        </a>
                                    </td>
                                    <td>
                                        <x-status-badge :status="$audit['status']">{{ ucfirst($audit['status']) }}</x-status-badge>
                                    </td>
                                    <td class="text-right">
                                        @if ($audit['quality_score'] !== null)
                                            <span class="ff-data font-semibold font-mono
                                                @if ($audit['quality_score'] >= 90) ff-score-excellent
                                                @elseif ($audit['quality_score'] >= 75) ff-score-good
                                                @elseif ($audit['quality_score'] >= 50) ff-score-acceptable
                                                @else ff-score-poor @endif
                                            ">{{ number_format($audit['quality_score'], 1) }}</span>
                                        @else
                                            <span class="text-gray-400">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="text-right ff-data">
                                        <span class="font-mono">{{ $audit['anomaly_count'] }}</span>
                                        @if ($audit['critical_anomaly_count'] > 0)
                                            <span class="text-danger-600 text-xs ml-1 font-mono">({{ $audit['critical_anomaly_count'] }})</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-600">{{ $audit['performer_name'] }}</td>
                                    <td class="text-right ff-data text-gray-500 font-mono text-xs">{{ $audit['created_at'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function initDashboardCharts() {
        if (typeof window.Chart === 'undefined') {
            return false;
        }

        const typeData = @json($data['projects_by_type']);
        const statusData = @json($data['projects_by_status']);
        const auditStatusData = @json($data['audits_by_status']);

        const typeLabels = Object.keys(typeData).map(k => k === 'transport' ? 'Transport' : 'Distribution');
        const typeValues = Object.values(typeData);

        const statusLabels = Object.keys(statusData);
        const statusValues = Object.values(statusData);

        const auditLabels = Object.keys(auditStatusData);
        const auditValues = Object.values(auditStatusData);

        const colorMap = {
            transport: '#1844D8',
            distribution: '#6B94FF',
            draft: '#D1D5DB',
            in_progress: '#3B6CFF',
            audited: '#F59E0B',
            validated: '#10B981',
            archived: '#9CA3AF',
            pending: '#F59E0B',
            running: '#3B6CFF',
            completed: '#10B981',
            failed: '#EF4444',
        };

        const chartDefaults = {
            responsive: true,
            maintainAspectRatio: false,
        };

        const monoFont = { family: "'JetBrains Mono', monospace", size: 11 };
        const tickGrid = {
            y: { beginAtZero: true, ticks: { ...monoFont }, grid: { color: '#F3F4F6' }, border: { display: false } },
            x: { grid: { display: false }, border: { display: false }, ticks: { ...monoFont } },
        };

        new Chart(document.getElementById('chartProjectType'), {
            type: 'doughnut',
            data: {
                labels: typeLabels,
                datasets: [{
                    data: typeValues,
                    backgroundColor: Object.keys(typeData).map(k => colorMap[k] || '#D1D5DB'),
                    borderWidth: 0,
                    hoverOffset: 4,
                }],
            },
            options: {
                ...chartDefaults,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: monoFont,
                        },
                    },
                },
            },
        });

        new Chart(document.getElementById('chartProjectStatus'), {
            type: 'bar',
            data: {
                labels: statusLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' ')),
                datasets: [{
                    data: statusValues,
                    backgroundColor: statusLabels.map(k => colorMap[k] || '#D1D5DB'),
                    borderRadius: 4,
                    barThickness: 24,
                }],
            },
            options: {
                ...chartDefaults,
                plugins: { legend: { display: false } },
                scales: { ...tickGrid },
            },
        });

        new Chart(document.getElementById('chartAuditStatus'), {
            type: 'bar',
            data: {
                labels: auditLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                datasets: [{
                    data: auditValues,
                    backgroundColor: auditLabels.map(k => colorMap[k] || '#D1D5DB'),
                    borderRadius: 4,
                    barThickness: 24,
                }],
            },
            options: {
                ...chartDefaults,
                plugins: { legend: { display: false } },
                scales: { ...tickGrid },
            },
        });

        return true;
        }

        if (! initDashboardCharts()) {
            document.addEventListener('DOMContentLoaded', () => initDashboardCharts());
        }
    </script>
    @endpush
</x-app-layout>
