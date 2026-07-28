<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $data = Cache::remember('dashboard', 300, function () {
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
                'recent_audits' => Audit::with('project', 'performer')
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                    ->map(fn ($audit) => [
                        'id' => $audit->id,
                        'project_id' => $audit->project_id,
                        'project_name' => $audit->project->name ?? '-',
                        'project_slug' => $audit->project->slug ?? '',
                        'status' => $audit->status->value,
                        'quality_score' => $audit->quality_score,
                        'anomaly_count' => $audit->anomaly_count,
                        'critical_anomaly_count' => $audit->critical_anomaly_count,
                        'performer_name' => $audit->performer->name ?? '-',
                        'created_at' => $audit->created_at->format('d/m/Y H:i'),
                    ])
                    ->toArray(),
            ];
        });

        return view('dashboard', compact('data'));
    }
}
