<?php

namespace App\Http\Controllers\Api;

use App\Enums\AuditStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditResource;
use App\Jobs\RunAuditJob;
use App\Models\Audit;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class AuditController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $this->authorize('viewAny', Audit::class);

        $audits = $project->audits()
            ->with('performer')
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

    public function show(Audit $audit): JsonResponse
    {
        $this->authorize('view', $audit);

        $audit->loadMissing(['performer', 'dataset']);

        return response()->json([
            'data' => new AuditResource($audit),
        ]);
    }
}
