<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function summary(User $user): array
    {
        return Cache::remember('dashboard.'.$user->id, 300, function () use ($user) {
            $projectsByType = Project::withoutTrashed()
                ->selectRaw('project_type, count(*) as count')
                ->groupBy('project_type')
                ->pluck('count', 'project_type')
                ->all();

            $projectsByStatus = Project::withoutTrashed()
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->all();

            $auditsByStatus = Audit::query()
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->all();

            return [
                'projects_count' => Project::withoutTrashed()->count(),
                'audits_count' => Audit::count(),
                'average_quality_score' => round((float) Audit::whereNotNull('quality_score')->avg('quality_score'), 1),
                'total_anomalies' => (int) Audit::sum('anomaly_count'),
                'total_critical_anomalies' => (int) Audit::sum('critical_anomaly_count'),
                'projects_by_type' => $projectsByType,
                'projects_by_status' => $projectsByStatus,
                'audits_by_status' => $auditsByStatus,
                'recent_audits' => $this->recentAudits($user)->all(),
            ];
        });
    }

    protected function recentAudits(User $user): Collection
    {
        return Audit::with('project', 'performer')
            ->when(! $user->isAdmin(), fn ($query) => $query->where('performed_by', $user->id))
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn (Audit $audit) => [
                'id' => $audit->id,
                'project_id' => $audit->project_id,
                'project_name' => $audit->project->name ?? '-',
                'project_slug' => $audit->project->slug ?? '',
                'status' => $audit->status->value,
                'quality_score' => $audit->quality_score,
                'anomaly_count' => $audit->anomaly_count,
                'critical_anomaly_count' => $audit->critical_anomaly_count,
                'performer_name' => $audit->performer->name ?? '-',
                'performer' => $audit->performer->name ?? '-',
                'created_at' => $audit->created_at?->format('M j, Y g:i A'),
                'completed_at' => $audit->completed_at?->toDateTimeString(),
            ]);
    }
}
