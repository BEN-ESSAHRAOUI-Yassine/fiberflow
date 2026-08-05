<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard summary statistics.
     *
     * @group Dashboard
     *
     * Returns aggregated data: project/audit counts, quality scores, anomaly totals,
     * breakdowns by type/status, and the 10 most recent audits. Data is cached for 5 minutes.
     *
     * @response {
     *   "data": {
     *     "projects_count": 15,
     *     "audits_count": 42,
     *     "average_quality_score": 82.5,
     *     "total_anomalies": 156,
     *     "total_critical_anomalies": 12,
     *     "projects_by_type": {"transport": 8, "distribution": 7},
     *     "projects_by_status": {"draft": 2, "in_progress": 5, "audited": 4, "validated": 3, "archived": 1},
     *     "audits_by_status": {"completed": 35, "pending": 5, "failed": 2},
     *     "recent_audits": [
     *       {
     *         "id": 42,
     *         "project_name": "FTTH Bordeaux Nord",
     *         "status": "completed",
     *         "quality_score": 85.3,
     *         "anomaly_count": 12,
     *         "critical_anomaly_count": 2,
     *         "performer": "Jean Dupont",
     *         "created_at": "2026-01-15T10:30:00.000000Z",
     *         "completed_at": "2026-01-15T10:35:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     */
    public function index(Request $request, DashboardService $dashboard): JsonResponse
    {
        $data = $dashboard->summary($request->user());

        $data['recent_audits'] = collect($data['recent_audits'])
            ->map(fn (array $audit) => [
                'id' => $audit['id'],
                'project_name' => $audit['project_name'],
                'status' => $audit['status'],
                'quality_score' => $audit['quality_score'],
                'anomaly_count' => $audit['anomaly_count'],
                'critical_anomaly_count' => $audit['critical_anomaly_count'],
                'performer' => $audit['performer'],
                'created_at' => $audit['created_at']?->toIso8601String(),
                'completed_at' => $audit['completed_at']?->toIso8601String(),
            ])
            ->all();

        return response()->json([
            'data' => new DashboardResource($data),
        ]);
    }
}
