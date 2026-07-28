<x-app-layout>
    <x-slot name="header">
        <h2 class="ff-page-title">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="ff-card p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">{{ __('Projects') }}</span>
                        <span class="ff-dot-info"></span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ $data['projects_count'] }}</div>
                </div>
                <div class="ff-card p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">{{ __('Audits') }}</span>
                        <span class="ff-dot-info"></span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ $data['audits_count'] }}</div>
                </div>
                <div class="ff-card p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">{{ __('Avg Quality') }}</span>
                        @if ($data['average_quality_score'] >= 90)
                            <span class="ff-dot-success"></span>
                        @elseif ($data['average_quality_score'] >= 75)
                            <span class="ff-dot-info"></span>
                        @elseif ($data['average_quality_score'] >= 50)
                            <span class="ff-dot-warning"></span>
                        @else
                            <span class="ff-dot-danger"></span>
                        @endif
                    </div>
                    <div class="text-3xl font-bold
                        @if ($data['average_quality_score'] >= 90) text-emerald-600
                        @elseif ($data['average_quality_score'] >= 75) text-brand-600
                        @elseif ($data['average_quality_score'] >= 50) text-amber-600
                        @else text-red-600 @endif
                    ">{{ $data['average_quality_score'] }}</div>
                </div>
                <div class="ff-card p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">{{ __('Anomalies') }}</span>
                        @if ($data['total_critical_anomalies'] > 0)
                            <span class="ff-dot-danger"></span>
                        @else
                            <span class="ff-dot-success"></span>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-gray-900">{{ number_format($data['total_anomalies']) }}</span>
                        @if ($data['total_critical_anomalies'] > 0)
                            <span class="text-sm font-medium text-red-600">{{ $data['total_critical_anomalies'] }} {{ __('critical') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="ff-card p-5">
                    <h3 class="ff-section-header mb-4">{{ __('Projects by Type') }}</h3>
                    <div style="height: 200px;">
                        <canvas id="chartProjectType"></canvas>
                    </div>
                </div>

                <div class="ff-card p-5">
                    <h3 class="ff-section-header mb-4">{{ __('Projects by Status') }}</h3>
                    <div style="height: 200px;">
                        <canvas id="chartProjectStatus"></canvas>
                    </div>
                </div>

                <div class="ff-card p-5">
                    <h3 class="ff-section-header mb-4">{{ __('Audits by Status') }}</h3>
                    <div style="height: 200px;">
                        <canvas id="chartAuditStatus"></canvas>
                    </div>
                </div>
            </div>

            {{-- Recent Audits --}}
            <div class="ff-card">
                <div class="p-5 border-b border-surface-100">
                    <h3 class="ff-section-header">{{ __('Recent Audits') }}</h3>
                </div>
                @if (empty($data['recent_audits']))
                    <div class="p-8 text-center text-sm text-gray-500">{{ __('No audits yet.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="ff-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Project') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Score') }}</th>
                                    <th>{{ __('Anomalies') }}</th>
                                    <th>{{ __('Performer') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['recent_audits'] as $audit)
                                <tr>
                                    <td class="font-mono text-gray-500">{{ $audit['id'] }}</td>
                                    <td>
                                        <a href="{{ route('admin.projects.audits.show', [$audit['project_id'], $audit['id']]) }}" class="font-medium text-brand-600 hover:text-brand-700">
                                            {{ $audit['project_name'] }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'completed' => ['class' => 'ff-badge-success', 'dot' => 'ff-dot-success'],
                                                'running' => ['class' => 'ff-badge-brand', 'dot' => 'ff-dot-info'],
                                                'pending' => ['class' => 'ff-badge-warning', 'dot' => 'ff-dot-warning'],
                                                'failed' => ['class' => 'ff-badge-danger', 'dot' => 'ff-dot-danger'],
                                            ];
                                            $s = $statusMap[$audit['status']] ?? ['class' => 'ff-badge-neutral', 'dot' => ''];
                                        @endphp
                                        <span class="{{ $s['class'] }}">
                                            <span class="{{ $s['dot'] }}"></span>
                                            {{ ucfirst($audit['status']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($audit['quality_score'] !== null)
                                            <span class="font-semibold
                                                @if ($audit['quality_score'] >= 90) text-emerald-600
                                                @elseif ($audit['quality_score'] >= 75) text-brand-600
                                                @elseif ($audit['quality_score'] >= 50) text-amber-600
                                                @else text-red-600 @endif
                                            ">{{ number_format($audit['quality_score'], 1) }}</span>
                                        @else
                                            <span class="text-gray-400">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $audit['anomaly_count'] }}
                                        @if ($audit['critical_anomaly_count'] > 0)
                                            <span class="text-red-500 text-xs ml-1">({{ $audit['critical_anomaly_count'] }})</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-600">{{ $audit['performer_name'] }}</td>
                                    <td class="text-gray-500">{{ $audit['created_at'] }}</td>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
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
                            font: { size: 12 },
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
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#F3F4F6' }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } },
                },
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
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#F3F4F6' }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } },
                },
            },
        });
    </script>
    @endpush
</x-app-layout>
