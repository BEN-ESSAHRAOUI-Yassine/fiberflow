<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Models\Audit;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(): JsonResponse
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
                        'project_name' => $audit->project->name ?? '-',
                        'status' => $audit->status->value,
                        'quality_score' => $audit->quality_score,
                        'anomaly_count' => $audit->anomaly_count,
                        'critical_anomaly_count' => $audit->critical_anomaly_count,
                        'performer' => $audit->performer->name ?? '-',
                        'created_at' => $audit->created_at->toIso8601String(),
                        'completed_at' => $audit->completed_at?->toIso8601String(),
                    ]),
            ];
        });

        return response()->json([
            'data' => new DashboardResource($data),
        ]);
    }
}
