<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DatasetResource;
use App\Models\Project;
use App\Models\ProjectDataset;
use App\Services\GISService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function __construct(
        private readonly GISService $gisService,
    ) {}

    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $datasets = $project->datasets()->orderByDesc('imported_at')->get();

        return response()->json([
            'data' => DatasetResource::collection($datasets),
        ]);
    }

    public function show(Project $project, ProjectDataset $dataset): JsonResponse
    {
        $this->authorize('view', $project);

        if ($dataset->project_id !== $project->id) {
            abort(404);
        }

        return response()->json([
            'data' => [
                'id' => $dataset->id,
                'project_id' => $dataset->project_id,
                'geojson' => $dataset->geojson,
                'imported_at' => $dataset->imported_at,
                'created_at' => $dataset->created_at,
                'updated_at' => $dataset->updated_at,
            ],
        ]);
    }

    public function import(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'schema' => ['required', 'string', 'in:apd_07,apd_08,rec_08'],
        ]);

        $result = $this->gisService->importFromPostGIS($validated['schema']);

        $dataset = $project->datasets()->create([
            'geojson' => $result['geojson'],
            'imported_at' => now(),
        ]);

        return response()->json([
            'data' => [
                'id' => $dataset->id,
                'project_id' => $dataset->project_id,
                'imported_at' => $dataset->imported_at,
                'counts' => $result['counts'],
            ],
        ], 201);
    }

    public function destroy(Project $project, ProjectDataset $dataset): JsonResponse
    {
        $this->authorize('update', $project);

        if ($dataset->project_id !== $project->id) {
            abort(404);
        }

        $dataset->delete();

        return response()->json(null, 204);
    }
}
