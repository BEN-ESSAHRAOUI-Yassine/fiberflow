<?php

namespace App\Http\Controllers\Api;

use App\Enums\AuditStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditResource;
use App\Jobs\RunAuditJob;
use App\Models\Audit;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * List audits for a project.
     *
     * @group Audits
     *
     * Returns a paginated list of audits for the given project, ordered by creation date (newest first).
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "project_id": 1,
     *       "projectdataset_id": 1,
     *       "performed_by": 1,
     *       "project_type_at_audit": "transport",
     *       "phase_at_audit": "APD",
     *       "status": "completed",
     *       "quality_score": 85.3,
     *       "connectivity_score": 90.1,
     *       "coherence_score": 82.7,
     *       "capacity_score": 78.5,
     *       "extensibility_score": 90.2,
     *       "anomaly_count": 12,
     *       "critical_anomaly_count": 2,
     *       "started_at": "2026-01-15T10:30:00.000000Z",
     *       "completed_at": "2026-01-15T10:35:00.000000Z",
     *       "created_at": "2026-01-15T10:30:00.000000Z",
     *       "performer": {"id": 1, "name": "Jean Dupont"}
     *     }
     *   ],
     *   "links": {},
     *   "meta": {"current_page": 1, "last_page": 1, "per_page": 15, "total": 1}
     * }
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function index(Project $project, Request $request): JsonResponse
    {
        $this->authorize('viewAny', Audit::class);

        $audits = $project->audits()
            ->with('performer')
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('performed_by', $request->user()->id))
            ->orderByDesc('created_at')
            ->paginate();

        return response()->json([
            'data' => AuditResource::collection($audits),
            'links' => $audits->linkCollection(),
            'meta' => [
                'current_page' => $audits->currentPage(),
                'last_page' => $audits->lastPage(),
                'per_page' => $audits->perPage(),
                'total' => $audits->total(),
            ],
        ]);
    }

    /**
     * Create and run a new audit for a project.
     *
     * The audit runs asynchronously in the background. The project must have at least one imported dataset.
     *
     * @group Audits
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @response 202 {
     *   "data": {
     *     "id": 1,
     *     "project_id": 1,
     *     "projectdataset_id": 1,
     *     "performed_by": 1,
     *     "project_type_at_audit": "transport",
     *     "phase_at_audit": "APD",
     *     "status": "pending",
     *     "quality_score": null,
     *     "anomaly_count": null,
     *     "critical_anomaly_count": null,
     *     "started_at": null,
     *     "completed_at": null,
     *     "created_at": "2026-01-15T10:30:00.000000Z"
     *   }
     * }
     * @response 422 scenario="No dataset" {"message": "Project has no dataset. Import a dataset before running an audit."}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function store(Project $project): JsonResponse
    {
        $this->authorize('create', Audit::class);

        $dataset = $project->datasets()->latest()->first();

        if (! $dataset) {
            return response()->json([
                'message' => __('Project has no dataset. Import a dataset before running an audit.'),
            ], 422);
        }

        $audit = Audit::create([
            'project_id' => $project->id,
            'projectdataset_id' => $dataset->id,
            'performed_by' => auth()->id(),
            'project_type_at_audit' => $project->project_type->value,
            'phase_at_audit' => $project->study_phase->value,
            'status' => AuditStatus::Pending,
        ]);

        RunAuditJob::dispatch($audit->id);

        return response()->json([
            'data' => new AuditResource($audit),
        ], 202);
    }

    /**
     * Get detailed information about a specific audit.
     *
     * Includes scores, network statistics, AI summary, recommendations, and anomalies.
     *
     * @group Audits
     *
     * @urlParam audit integer required The audit ID. Example: 1
     *
     * @response {
     *   "data": {
     *     "id": 1,
     *     "project_id": 1,
     *     "projectdataset_id": 1,
     *     "performed_by": 1,
     *     "project_type_at_audit": "transport",
     *     "phase_at_audit": "APD",
     *     "status": "completed",
     *     "quality_score": 85.3,
     *     "connectivity_score": 90.1,
     *     "coherence_score": 82.7,
     *     "capacity_score": 78.5,
     *     "extensibility_score": 90.2,
     *     "network_statistics": {
     *       "total_nodes": 150,
     *       "total_edges": 230,
     *       "total_fiber_km": 45.2
     *     },
     *     "ai_summary": "The network shows good overall connectivity...",
     *     "recommendations": [
     *       "Consider adding redundant paths in sector 3",
     *       "Upgrade capacity in high-traffic areas"
     *     ],
     *     "anomaly_count": 12,
     *     "critical_anomaly_count": 2,
     *     "model_used": "gpt-4",
     *     "tokens_used": 1250,
     *     "error_message": null,
     *     "started_at": "2026-01-15T10:30:00.000000Z",
     *     "completed_at": "2026-01-15T10:35:00.000000Z",
     *     "created_at": "2026-01-15T10:30:00.000000Z",
     *     "performer": {"id": 1, "name": "Jean Dupont"},
     *     "dataset": {"id": 1, "project_id": 1, "imported_at": "2026-01-15T09:00:00.000000Z"}
     *   }
     * }
     * @response 404 scenario="Not found" {"message": "Audit not found."}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function show(Audit $audit): JsonResponse
    {
        $this->authorize('view', $audit);

        $audit->loadMissing(['performer', 'dataset']);

        return response()->json([
            'data' => new AuditResource($audit),
        ]);
    }

    /**
     * Retry a failed or stalled audit.
     *
     * Resets the audit to pending and re-dispatches the run job.
     * Stalled audits are those stuck in "running" for more than 30 minutes.
     *
     * @group Audits
     *
     * @urlParam audit integer required The audit ID. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "status": "pending"
     *   }
     * }
     * @response 422 scenario="Not retryable" {"message": "Only failed or stalled audits can be retried."}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function retry(Audit $audit): JsonResponse
    {
        $this->authorize('view', $audit);

        if ($audit->status !== AuditStatus::Failed
            && ! ($audit->status === AuditStatus::Running && $audit->updated_at->lt(now()->subMinutes(30)))) {
            return response()->json([
                'message' => __('Only failed or stalled audits can be retried.'),
            ], 422);
        }

        $audit->update([
            'status' => AuditStatus::Pending,
            'error_message' => null,
        ]);

        RunAuditJob::dispatch($audit->id);

        return response()->json([
            'data' => new AuditResource($audit),
        ]);
    }
}
