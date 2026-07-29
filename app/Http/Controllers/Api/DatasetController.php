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

    /**
     * List datasets for a project.
     *
     * @group Datasets
     *
     * Returns all imported datasets for the given project, ordered by import date (newest first).
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "project_id": 1,
     *       "imported_at": "2026-01-15T09:00:00.000000Z",
     *       "created_at": "2026-01-15T09:00:00.000000Z",
     *       "updated_at": "2026-01-15T09:00:00.000000Z"
     *     }
     *   ]
     * }
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $datasets = $project->datasets()->orderByDesc('imported_at')->get();

        return response()->json([
            'data' => DatasetResource::collection($datasets),
        ]);
    }

    /**
     * Get a specific dataset with its full GeoJSON data.
     *
     * @group Datasets
     *
     * @urlParam project integer required The project ID. Example: 1
     * @urlParam dataset integer required The dataset ID. Example: 1
     *
     * @response {
     *   "data": {
     *     "id": 1,
     *     "project_id": 1,
     *     "geojson": {
     *       "fiber_segments": [...],
     *       "nodes": [...],
     *       "splitters": [...]
     *     },
     *     "imported_at": "2026-01-15T09:00:00.000000Z",
     *     "created_at": "2026-01-15T09:00:00.000000Z",
     *     "updated_at": "2026-01-15T09:00:00.000000Z"
     *   }
     * }
     * @response 404 scenario="Not found" {"message": "Dataset not found."}
     */
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

    /**
     * Import a new dataset from PostGIS into the project.
     *
     * Fetches GIS data from the specified schema and stores it as a GeoJSON dataset.
     *
     * @group Datasets
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @bodyParam schema string required The PostGIS schema to import from. Enum: apd_07, apd_08, rec_08. Example: apd_08
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "project_id": 1,
     *     "imported_at": "2026-01-15T09:00:00.000000Z",
     *     "counts": {
     *       "fiber_segments": 150,
     *       "nodes": 45,
     *       "splitters": 12
     *     }
     *   }
     * }
     * @response 422 scenario="Invalid schema" {"message": "The selected schema is invalid.", "errors": {"schema": ["The selected schema is invalid."]}}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
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

    /**
     * Delete a dataset.
     *
     * Permanently deletes the dataset and its GeoJSON data.
     *
     * @group Datasets
     *
     * @urlParam project integer required The project ID. Example: 1
     * @urlParam dataset integer required The dataset ID. Example: 1
     *
     * @response 204 scenario="Deleted successfully"
     * @response 404 scenario="Not found" {"message": "Dataset not found."}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
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
